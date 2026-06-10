<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirController extends Controller
{
    private $apiKey;
    private $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY', config('services.rajaongkir.api_key'));
    }

    /**
     * Cari area (kecamatan/kodepos) untuk dropdown form checkout
     */
    public function searchArea(Request $request)
    {
        $search = $request->query('q');

        if (!$search || strlen($search) < 3) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/domestic-destination", [
                'search' => $search,
                'limit' => 10,
                'offset' => 0
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $areas = $data['data'] ?? [];
                
                $formatted = array_map(function($area) {
                    return [
                        'id' => $area['id'], // Subdistrict ID
                        'text' => $area['label'], // e.g. GROGOL, JAKARTA BARAT, DKI JAKARTA, 11450
                        'postal_code' => $area['zip_code'],
                        'city' => $area['city_name'],
                        'province' => $area['province_name']
                    ];
                }, $areas);

                return response()->json($formatted);
            }

            return response()->json([]);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Hitung ongkos kirim (rates) via RajaOngkir V2
     */
    public function getRates(Request $request)
    {
        $request->validate([
            'destination_area_id' => 'required',
            'weight' => 'required|numeric' // in grams
        ]);

        // Origin set to Kebayoran Lama (Subdistrict ID 17464) as default
        $originSubdistrictId = env('RAJAONGKIR_ORIGIN_ID', 17464); 
        $weight = ceil($request->weight);
        if ($weight < 1) $weight = 1000; // minimum 1kg
        
        $couriers = ['jne', 'pos', 'tiki'];
        $formattedCosts = [];

        try {
            foreach ($couriers as $courier) {
                $response = Http::asForm()->withHeaders([
                    'key' => $this->apiKey,
                ])->post("{$this->baseUrl}/calculate/domestic-cost", [
                    'origin' => (int)$originSubdistrictId,
                    'destination' => (int)$request->destination_area_id,
                    'weight' => (int)$weight,
                    'courier' => $courier
                ]);

                if ($response->successful()) {
                    $results = $response->json()['data'] ?? [];
                    if (!empty($results) && !empty($results[0]['costs'])) {
                        $costs = $results[0]['costs'];
                        foreach ($costs as $costDetail) {
                            $formattedCosts[] = [
                                'service' => strtoupper($courier) . ' - ' . $costDetail['service'],
                                'description' => $costDetail['description'],
                                'cost' => $costDetail['cost'],
                                'etd' => $costDetail['etd']
                            ];
                        }
                    }
                }
            }

            return response()->json(['costs' => $formattedCosts]);

        } catch (\Exception $e) {
            Log::error('RajaOngkir V2 Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem'], 500);
        }
    }
}

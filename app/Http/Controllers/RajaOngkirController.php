<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirController extends Controller
{
    private $apiKey;
    private $baseUrl = 'https://api-sandbox.collaborator.komerce.id';

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY', '1oKzkr5Qf967fe03de1d601bxUErSPD8');
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
                'x-api-key' => $this->apiKey,
            ])->get("{$this->baseUrl}/tariff/api/v1/destination/search", [
                'keyword' => $search
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $areas = $data['data'] ?? [];
                
                $formatted = array_map(function($area) {
                    return [
                        'id' => $area['id'],
                        'text' => $area['label'],
                        'postal_code' => $area['zip_code'] ?? '',
                        'city' => $area['city_name'] ?? '',
                        'province' => $area['district_name'] ?? '' // Using district_name as province is not in the response
                    ];
                }, $areas);

                return response()->json($formatted);
            }

            return response()->json([]);
        } catch (\Exception $e) {
            Log::error('Komship Search Error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Hitung ongkos kirim (rates) via Komship API
     */
    public function getRates(Request $request)
    {
        $request->validate([
            'destination_area_id' => 'required',
            'weight' => 'required|numeric' // frontend sends in grams
        ]);

        // Origin set to Kebayoran Lama (Subdistrict ID 31597 for example, or we use a fallback if not provided)
        $originSubdistrictId = env('KOMSHIP_ORIGIN_ID', 31597); // 31597 is just an example ID from the doc
        
        // Komship expects weight in KG (float)
        $weightInKg = $request->weight / 1000;
        if ($weightInKg < 1) $weightInKg = 1; // minimum 1kg
        
        $formattedCosts = [];

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->get("{$this->baseUrl}/tariff/api/v1/calculate", [
                'shipper_destination_id' => (int)$originSubdistrictId,
                'receiver_destination_id' => (int)$request->destination_area_id,
                'weight' => (float)$weightInKg,
                'item_value' => 10000, // Dummy value since we just want the shipping cost
                'cod' => 'no'
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                
                // Combine reguler and cargo options
                $reguler = $data['calculate_reguler'] ?? [];
                $cargo = $data['calculate_cargo'] ?? [];
                $allOptions = array_merge($reguler, $cargo);

                foreach ($allOptions as $costDetail) {
                    $formattedCosts[] = [
                        'courier' => $costDetail['shipping_name'],
                        'service' => $costDetail['shipping_name'] . ' - ' . $costDetail['service_name'],
                        'description' => $costDetail['etd'] ? "Estimasi {$costDetail['etd']}" : "Layanan {$costDetail['service_name']}",
                        'cost' => $costDetail['shipping_cost_net'] ?? $costDetail['shipping_cost'],
                        'etd' => $costDetail['etd']
                    ];
                }
            } else {
                Log::error('Komship Calculate Failed: ' . $response->body());
            }

            return response()->json(['costs' => $formattedCosts]);

        } catch (\Exception $e) {
            Log::error('Komship Calculate Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BiteshipController extends Controller
{
    private $apiKey;
    private $baseUrl = 'https://api.biteship.com/v1';

    public function __construct()
    {
        $this->apiKey = env('BITESHIP_API_KEY', 'biteship_test_dummy_key');
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
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/maps/areas", [
                'countries' => 'ID',
                'input' => $search,
                'type' => 'single'
            ]);

            if ($response->successful()) {
                // Biteship returns 'areas' array
                $data = $response->json();
                $areas = $data['areas'] ?? [];
                
                $formatted = array_map(function($area) {
                    return [
                        'id' => $area['id'],
                        'text' => $area['name'] . ', ' . $area['administrative_division_level_2_name'] . ', ' . $area['administrative_division_level_1_name'] . ' - ' . $area['postal_code']
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
     * Hitung ongkos kirim (rates)
     */
    public function getRates(Request $request)
    {
        $request->validate([
            'destination_area_id' => 'required',
            'weight' => 'required|numeric' // in grams
        ]);

        $originAreaId = env('BITESHIP_ORIGIN_AREA_ID', 'IDNP3IDNC131IDND1489IDZ12440'); // default area

        try {
            // Biteship expects weight in grams if items are passed, or just use weight
            // But biteship rates API expects origin_area_id, destination_area_id, couriers, and items.
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/rates/couriers", [
                'origin_area_id' => $originAreaId,
                'destination_area_id' => $request->destination_area_id,
                'couriers' => 'jne,jnt,sicepat,anteraja', // ask for multiple couriers
                'items' => [
                    [
                        'name' => 'Package',
                        'description' => 'Items',
                        'value' => 100000,
                        'length' => 10,
                        'width' => 10,
                        'height' => 10,
                        'weight' => $request->weight,
                        'quantity' => 1
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Format response to match our frontend needs
                $formattedCosts = [];
                $pricing = $data['pricing'] ?? [];
                
                foreach ($pricing as $courier) {
                    $formattedCosts[] = [
                        'service' => strtoupper($courier['courier_name']) . ' - ' . $courier['courier_service_name'],
                        'description' => $courier['courier_service_name'],
                        'cost' => $courier['price'],
                        'etd' => $courier['duration'] // e.g. "1-2 days"
                    ];
                }
                
                return response()->json(['costs' => $formattedCosts]);
            }

            return response()->json(['error' => 'Gagal mengambil tarif', 'details' => $response->json()], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan sistem'], 500);
        }
    }

    /**
     * Create a real order in Biteship (Booking Kurir)
     */
    public function createOrder($order)
    {
        $originPostalCode = env('BITESHIP_ORIGIN_POSTAL_CODE', '12310');
        $address = $order->address;
        $shipment = $order->shipment;

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/orders", [
                'origin_contact_name' => 'DjudasMS Official',
                'origin_contact_phone' => '081234567890',
                'origin_address' => 'Gudang Utama DjudasMS',
                'origin_postal_code' => $originPostalCode,
                
                'destination_contact_name' => $address->name,
                'destination_contact_phone' => $address->phone,
                'destination_address' => $address->address . ', ' . $address->city . ', ' . $address->province,
                'destination_postal_code' => $address->postal_code,
                
                'courier_company' => strtolower($shipment->courier),
                'courier_type' => strtolower(explode(' ', $shipment->service)[0] ?? 'reg'),
                
                'delivery_type' => 'now',
                
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->product_name,
                        'value' => $item->price,
                        'quantity' => $item->quantity,
                        'weight' => $item->product ? ($item->product->weight ?? 1000) : 1000
                    ];
                })->toArray(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'biteship_order_id' => $data['id'] ?? null,
                    'waybill_id' => $data['courier']['waybill_id'] ?? null,
                    'tracking_id' => $data['courier']['tracking_id'] ?? null,
                ];
            }

            return ['success' => false, 'message' => 'Failed to create Biteship order', 'details' => $response->json()];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Ambil tracking real-time
     */
    public function getTracking($biteshipOrderId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/orders/{$biteshipOrderId}");

            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}

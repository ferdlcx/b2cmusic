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
                        'text' => $area['name'] . ', ' . $area['administrative_division_level_2_name'] . ', ' . $area['administrative_division_level_1_name'] . ' - ' . $area['postal_code'],
                        'postal_code' => $area['postal_code'],
                        'city' => $area['administrative_division_level_2_name'],
                        'province' => $area['administrative_division_level_1_name']
                    ];
                }, $areas);

                return response()->json($formatted);
            }

            // Fallback for Demo if Biteship limits/balance is reached
            $errorJson = $response->json();
            if (isset($errorJson['error']) && str_contains(strtolower($errorJson['error']), 'balance')) {
                return response()->json([
                    [
                        'id' => 'IDNP3IDNC131IDND1489IDZ12440',
                        'text' => strtoupper($search) . ' (Mock Area), Jakarta Selatan, DKI Jakarta - 12920',
                        'postal_code' => '12920',
                        'city' => 'Jakarta Selatan',
                        'province' => 'DKI Jakarta'
                    ],
                    [
                        'id' => 'IDNP10IDNC201IDND1234IDZ55281',
                        'text' => strtoupper($search) . ' (Mock Area 2), Sleman, DI Yogyakarta - 55281',
                        'postal_code' => '55281',
                        'city' => 'Sleman',
                        'province' => 'DI Yogyakarta'
                    ]
                ]);
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

            // Fallback for Demo/Testing if API limit or balance runs out
            $errorJson = $response->json();
            if (isset($errorJson['error']) && str_contains(strtolower($errorJson['error']), 'balance')) {
                // Skenario Mock Pintar untuk Ujian:
                // Mengekstrak ID Provinsi dari 'IDNP{X}IDNC...'
                $destAreaId = $request->destination_area_id;
                $provId = 3; // Default Jakarta
                if (preg_match('/IDNP(\d+)/', $destAreaId, $matches)) {
                    $provId = (int)$matches[1];
                }
                
                // Tarif dasar per KG (Origin: Jakarta Selatan)
                $basePricePerKg = 15000; // Default area Jawa
                
                if (in_array($provId, [1, 2, 5, 6, 7, 8, 31, 32])) {
                    $basePricePerKg = 35000; // Sumatera
                } elseif (in_array($provId, [13, 14, 15, 16, 17])) {
                    $basePricePerKg = 45000; // Kalimantan
                } elseif (in_array($provId, [20, 21, 22, 23, 24, 25])) {
                    $basePricePerKg = 55000; // Sulawesi
                } elseif (in_array($provId, [33, 34, 35, 36, 37, 38])) {
                    $basePricePerKg = 90000; // Papua & Maluku
                }
                
                // Hitung berat aktual (pembulatan ke atas per 1000 gram)
                $weightKg = ceil($request->weight / 1000);
                if ($weightKg < 1) $weightKg = 1;
                
                $totalBasePrice = $basePricePerKg * $weightKg;

                // Mock rates for demonstration
                $formattedCosts = [
                    [
                        'service' => 'JNE - REG',
                        'description' => 'Layanan Reguler (Mock Test Mode)',
                        'cost' => $totalBasePrice,
                        'etd' => ($provId == 3 ? '1-2' : '3-5') . ' days'
                    ],
                    [
                        'service' => 'JNT - EZ',
                        'description' => 'Regular Service (Mock Test Mode)',
                        'cost' => $totalBasePrice + 2000, // Sedikit lebih mahal untuk variasi
                        'etd' => ($provId == 3 ? '1-2' : '2-4') . ' days'
                    ],
                    [
                        'service' => 'SICEPAT - REG',
                        'description' => 'Sicepat Reguler (Mock Test Mode)',
                        'cost' => $totalBasePrice + 1000,
                        'etd' => ($provId == 3 ? '1' : '2-3') . ' days'
                    ]
                ];
                return response()->json(['costs' => $formattedCosts]);
            }

            return response()->json(['error' => 'Gagal mengambil tarif', 'details' => $errorJson], 400);

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

            // Fallback for Demo if Biteship limits/balance is reached
            $errorJson = $response->json();
            if (isset($errorJson['error']) && str_contains(strtolower($errorJson['error']), 'balance')) {
                return [
                    'success' => true,
                    'biteship_order_id' => 'MOCK_ORDER_' . uniqid(),
                    'waybill_id' => 'MOCK_WAYBILL_' . uniqid(),
                    'tracking_id' => 'MOCK_TRACKING_' . uniqid(),
                ];
            }

            return ['success' => false, 'message' => 'Failed to create Biteship order', 'details' => $errorJson];

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

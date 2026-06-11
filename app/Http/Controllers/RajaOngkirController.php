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
     * Cari area (kecamatan/kodepos) untuk dropdown form checkout via Biteship Maps API
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
                        'id' => $area['id'],
                        'text' => $area['label'],
                        'postal_code' => $area['zip_code'],
                        'city' => $area['city_name'],
                        'province' => $area['province_name']
                    ];
                }, $areas);

                return response()->json($formatted);
            }
            
            Log::warning('RajaOngkir Maps Failed. Status: ' . $response->status() . ' Body: ' . $response->body());
            return response()->json([
                [
                    'id' => '17464', // Dummy ID
                    'text' => ucwords($search) . ' (Simulasi Bebas/API Limit)',
                    'postal_code' => '12345',
                    'city' => ucwords($search),
                    'province' => 'Provinsi Mock'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('RajaOngkir Maps Error: ' . $e->getMessage());
            return response()->json([
                [
                    'id' => '17464',
                    'text' => ucwords($search) . ' (Simulasi Bebas/API Error)',
                    'postal_code' => '12345',
                    'city' => ucwords($search),
                    'province' => 'Provinsi Mock'
                ]
            ]);
        }
    }

    /**
     * Hitung jarak (Haversine Formula) dalam KM
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }

    /**
     * Estimasi koordinat kasar berdasarkan kota/provinsi
     */
    private function getApproximateCoordinates($cityName, $provinceName)
    {
        $city = strtolower($cityName);
        $province = strtolower($provinceName);
        
        // Default (Jakarta Selatan)
        $lat = -6.2253;
        $lng = 106.7994;
        
        if (str_contains($city, 'bandung')) {
            $lat = -6.9175; $lng = 107.6191;
        } elseif (str_contains($city, 'surabaya')) {
            $lat = -7.2575; $lng = 112.7521;
        } elseif (str_contains($city, 'semarang')) {
            $lat = -6.9667; $lng = 110.4167;
        } elseif (str_contains($city, 'yogyakarta') || str_contains($city, 'jogja')) {
            $lat = -7.7956; $lng = 110.3695;
        } elseif (str_contains($city, 'medan')) {
            $lat = 3.5952; $lng = 98.6722;
        } elseif (str_contains($city, 'makassar')) {
            $lat = -5.1477; $lng = 119.4327;
        } elseif (str_contains($city, 'denpasar') || str_contains($province, 'bali')) {
            $lat = -8.6705; $lng = 115.2126;
        } elseif (str_contains($city, 'palembang')) {
            $lat = -2.9909; $lng = 104.7566;
        } elseif (str_contains($city, 'padang')) {
            $lat = -0.9471; $lng = 100.4172;
        } elseif (str_contains($city, 'balikpapan')) {
            $lat = -1.2654; $lng = 116.8312;
        } elseif (str_contains($city, 'samarinda')) {
            $lat = -0.5022; $lng = 117.1536;
        } elseif (str_contains($city, 'pontianak')) {
            $lat = -0.0263; $lng = 109.3425;
        } elseif (str_contains($city, 'manado')) {
            $lat = 1.4748; $lng = 124.8428;
        } elseif (str_contains($city, 'ambon')) {
            $lat = -3.6954; $lng = 128.1814;
        } elseif (str_contains($city, 'jayapura') || str_contains($province, 'papua')) {
            $lat = -2.5916; $lng = 140.7181;
        } elseif (str_contains($province, 'banten') || str_contains($city, 'serang') || str_contains($city, 'tangerang')) {
            $lat = -6.1200; $lng = 106.1502;
        } elseif (str_contains($province, 'jawa barat')) {
            $lat = -6.9175; $lng = 107.6191;
        } elseif (str_contains($province, 'jawa tengah')) {
            $lat = -7.0000; $lng = 110.0000;
        } elseif (str_contains($province, 'jawa timur')) {
            $lat = -7.5360; $lng = 112.2384;
        } elseif (str_contains($province, 'sumatera')) {
            $lat = -0.5897; $lng = 101.3431;
        } elseif (str_contains($province, 'kalimantan')) {
            $lat = -0.9730; $lng = 114.0308;
        } elseif (str_contains($province, 'sulawesi')) {
            $lat = -1.9023; $lng = 120.1219;
        } elseif (str_contains($province, 'nusa tenggara') || str_contains($province, 'ntb') || str_contains($province, 'ntt')) {
            $lat = -8.6529; $lng = 121.0791;
        } elseif (str_contains($province, 'maluku')) {
            $lat = -3.2384; $lng = 130.1453;
        }
        
        return ['latitude' => $lat, 'longitude' => $lng];
    }

    /**
     * Hitung ongkos kirim (rates) via Biteship (Migration from RajaOngkir)
     */
    public function getRates(Request $request)
    {
        $request->validate([
            'destination_area_id' => 'required',
            'weight' => 'required|numeric' // in grams
        ]);

        $weight = ceil($request->weight);
        if ($weight < 1) $weight = 1000; // minimum 1kg
        
        // Hitung jarak KM untuk info tambahan
        $lat = $request->latitude;
        $lng = $request->longitude;

        if (!$lat || !$lng) {
            $address = \App\Models\Address::where('area_id', $request->destination_area_id)
                ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->first();
            if ($address && $address->latitude && $address->longitude) {
                $lat = $address->latitude;
                $lng = $address->longitude;
            }
        }

        if (!$lat || !$lng) {
            $approx = $this->getApproximateCoordinates($request->city ?? '', $request->province ?? '');
            $lat = $approx['latitude'];
            $lng = $approx['longitude'];
        }

        $originLat = -6.2253;
        $originLng = 106.7994;
        $distance = $this->calculateDistance($originLat, $originLng, $lat, $lng);

        $originSubdistrictId = env('RAJAONGKIR_ORIGIN_ID', 17464); 
        $couriers = ['jne', 'pos', 'tiki'];
        $formattedCosts = [];

        try {
            $hasApiSuccess = false;
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
                    if (!empty($results)) {
                        $hasApiSuccess = true;
                        foreach ($results as $costDetail) {
                            $formattedCosts[] = [
                                'service' => strtoupper($courier) . ' - ' . ($costDetail['service'] ?? ''),
                                'description' => $costDetail['description'] ?? '',
                                'cost' => $costDetail['cost'] ?? 0,
                                'etd' => $costDetail['etd'] ?? ''
                            ];
                        }
                    }
                } else {
                    Log::warning("RajaOngkir Rates Failed for $courier. Body: " . $response->body());
                }
            }

            // Jika semua gagal (termasuk karena limit/429 atau area ID dummy), gunakan mock!
            if (!$hasApiSuccess) {
                Log::warning('RajaOngkir Limits Reached. Using Mock Rates.');
                $formattedCosts = $this->getMockRates($distance, $weight);
            }

            return response()->json([
                'costs' => $formattedCosts,
                'distance' => $distance
            ]);
        } catch (\Exception $e) {
            Log::error('RajaOngkir V2 Error: ' . $e->getMessage());
            return response()->json([
                'costs' => $this->getMockRates($distance, $weight),
                'distance' => $distance
            ]);
        }
    }

    /**
     * MOCK DATA (Fallback jika Biteship limit/saldo habis saat testing)
     * Mengkalkulasi harga berdasarkan jarak * 500 + (berat * 10)
     */
    private function getMockRates($distance, $weight)
    {
        $basePrice = 10000;
        $distanceCost = $distance * 250;
        $weightCost = ($weight / 1000) * 5000;
        
        $totalCost = round($basePrice + $distanceCost + $weightCost);
        // Bulatkan ke ribuan terdekat
        $totalCost = ceil($totalCost / 1000) * 1000;

        return [
            [
                'service' => 'JNE - REG (MOCK)',
                'description' => 'Layanan Reguler',
                'cost' => $totalCost,
                'etd' => '2-3 Hari'
            ],
            [
                'service' => 'SICEPAT - HALU (MOCK)',
                'description' => 'Harga Mulai Lima Ribu',
                'cost' => max(5000, $totalCost - 3000),
                'etd' => '3-5 Hari'
            ],
            [
                'service' => 'J&T - EZ (MOCK)',
                'description' => 'Reguler Service',
                'cost' => $totalCost + 2000,
                'etd' => '2-3 Hari'
            ]
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\OrderCoupon;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Notifications\OrderCreated;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return redirect()->route('catalog')->with('error', 'Keranjang Anda kosong.');
        }

        $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('catalog')->with('error', 'Keranjang Anda kosong.');
        }

        $addresses = Address::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('is_default', true)->first() ?: $addresses->first();

        $subtotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        // Use flat rates or calculate shipping cost via frontend in the future
        $shippingCost = 25000; // default flat rate

        return view('cart.checkout', compact('cartItems', 'addresses', 'defaultAddress', 'subtotal', 'shippingCost'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();
        $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('catalog')->with('error', 'Keranjang Anda kosong.');
        }

        $request->validate([
            'address_id' => ['required', 'exists:addresses,id'],
            'courier' => ['required', 'string'],
            'payment_method' => ['required', 'in:va,ewallet,credit_card,qris'],
            'coupon_code' => ['nullable', 'string', 'exists:coupons,code'],
        ]);

        // Verify Address
        $address = Address::where('user_id', $user->id)->findOrFail($request->address_id);

        // Verify Stock again
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Stok produk {$item->product->name} tidak mencukupi.");
            }
        }

        // Calculate Totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        // Calculate total weight of cart
        $totalWeight = $cartItems->sum(function($item) {
            return ($item->product->weight ?: 1000) * $item->quantity;
        });

        // Calculate shipping cost using RajaOngkir cost API if key is set
        $shippingCost = $request->courier === 'JNE_YES' ? 40000 : 25000; // default fallback
        $apiKey = config('services.rajaongkir.api_key');
        $originCity = config('services.rajaongkir.origin_city_id', 152); // default Jakarta Barat
        
        $selectedCourier = 'jne';
        if (str_contains(strtolower($request->courier), 'pos')) {
            $selectedCourier = 'pos';
        } elseif (str_contains(strtolower($request->courier), 'jnt')) {
            $selectedCourier = 'tiki'; // Starter API only supports jne, pos, tiki
        }

        if ($apiKey && $address->city_id) {
            try {
                $response = Http::withoutVerifying()->timeout(5)->withHeaders([
                    'key' => $apiKey
                ])->post('https://api.rajaongkir.com/starter/cost', [
                    'origin' => (int) $originCity,
                    'destination' => (int) $address->city_id,
                    'weight' => (int) $totalWeight,
                    'courier' => $selectedCourier
                ]);
                
                if ($response->successful()) {
                    $results = $response->json('rajaongkir.results.0.costs');
                    if ($results && count($results) > 0) {
                        $costVal = null;
                        $isYes = str_contains(strtoupper($request->courier), 'YES');
                        foreach ($results as $c) {
                            if ($isYes && str_contains(strtoupper($c['service']), 'YES')) {
                                $costVal = $c['cost'][0]['value'];
                                break;
                            } elseif (!$isYes && str_contains(strtoupper($c['service']), 'REG')) {
                                $costVal = $c['cost'][0]['value'];
                                break;
                            }
                        }
                        if (is_null($costVal)) {
                            $costVal = $results[0]['cost'][0]['value'];
                        }
                        $shippingCost = $costVal;
                    }
                }
            } catch (\Exception $e) {
                logger()->error('RajaOngkir cost calculation failed on process: ' . $e->getMessage());
            }
        }

        $discount = 0;
        $coupon = null;

        // Apply Coupon if present
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('status', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if ($coupon) {
                if ($subtotal >= $coupon->min_purchase) {
                    if ($coupon->type === 'percent') {
                        $discount = $subtotal * ($coupon->value / 100);
                        if ($coupon->max_discount && $discount > $coupon->max_discount) {
                            $discount = $coupon->max_discount;
                        }
                    } else {
                        $discount = $coupon->value;
                    }
                } else {
                    return back()->with('error', "Pembelian minimum untuk menggunakan kupon {$coupon->code} adalah Rp " . number_format($coupon->min_purchase, 0, ',', '.'));
                }
            }
        }

        $total = ($subtotal + $shippingCost) - $discount;
        if ($total < 0) $total = 0;

        // Start Database Transaction based on ERD structure
        DB::beginTransaction();
        try {
            // 1. Create Order
            $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address_id' => $address->id,
                'order_code' => $orderCode,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
            ]);

            // 2. Create Order Items & Deduct Stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->price * $item->quantity,
                ]);

                // Update product stock
                $product = Product::findOrFail($item->product_id);
                $product->decrement('stock', $item->quantity);
            }

            // 3. Create Shipment
            $courierName = $request->courier === 'JNE_YES' ? 'JNE' : ($request->courier === 'JNT' ? 'J&T' : 'POS');
            $serviceName = $request->courier === 'JNE_YES' ? 'YES' : 'REG';
            Shipment::create([
                'order_id' => $order->id,
                'courier' => $courierName,
                'service' => $serviceName,
                'tracking_number' => null, // Generated when shipped by admin
                'shipping_cost' => $shippingCost,
                'status' => 'shipped', // Default status in ERD
                'shipped_at' => null,
                'delivered_at' => null,
            ]);

            // 4. Request Midtrans Snap Token
            $snapToken = null;
            $redirectUrl = null;
            $midtransServerKey = config('services.midtrans.server_key');
            
            if ($midtransServerKey) {
                try {
                    $itemDetails = [];
                    foreach ($cartItems as $item) {
                        $itemDetails[] = [
                            'id' => (string) $item->product_id,
                            'price' => (int) $item->price,
                            'quantity' => (int) $item->quantity,
                            'name' => substr($item->product->name, 0, 50),
                        ];
                    }
                    
                    if ($shippingCost > 0) {
                        $itemDetails[] = [
                            'id' => 'SHIPPING',
                            'price' => (int) $shippingCost,
                            'quantity' => 1,
                            'name' => 'Shipping Cost (' . $request->courier . ')',
                        ];
                    }
                    
                    if ($discount > 0) {
                        $itemDetails[] = [
                            'id' => 'DISCOUNT',
                            'price' => -((int) $discount),
                            'quantity' => 1,
                            'name' => 'Coupon Discount',
                        ];
                    }
                    
                    $enabledPayments = [];
                    switch ($request->payment_method) {
                        case 'qris':
                            $enabledPayments = ['gopay', 'shopeepay', 'other_qris'];
                            break;
                        case 'va':
                            $enabledPayments = ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va'];
                            break;
                        case 'credit_card':
                            $enabledPayments = ['credit_card'];
                            break;
                        case 'ewallet':
                            $enabledPayments = ['gopay', 'shopeepay'];
                            break;
                    }

                    $payload = [
                        'transaction_details' => [
                            'order_id' => $orderCode,
                            'gross_amount' => (int) $total,
                        ],
                        'item_details' => $itemDetails,
                        'enabled_payments' => $enabledPayments,
                        'customer_details' => [
                            'first_name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone ?: '081234567890',
                            'billing_address' => [
                                'first_name' => $user->name,
                                'phone' => $user->phone ?: '081234567890',
                                'address' => $address->address,
                                'city' => $address->city,
                                'postal_code' => $address->postal_code,
                            ],
                            'shipping_address' => [
                                'first_name' => $address->name,
                                'phone' => $address->phone,
                                'address' => $address->address,
                                'city' => $address->city,
                                'postal_code' => $address->postal_code,
                            ],
                        ],
                        'callbacks' => [
                            'finish' => route('orders.show', $orderCode)
                        ]
                    ];
                    
                    $response = Http::withoutVerifying()->timeout(10)->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->withBasicAuth($midtransServerKey, '')
                      ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $payload);
                      
                    if ($response->successful()) {
                        $snapToken = $response->json('token');
                        $redirectUrl = $response->json('redirect_url');
                    } else {
                        logger()->error('Midtrans Snap error: ' . $response->body());
                    }
                } catch (\Exception $e) {
                    logger()->error('Midtrans API exception: ' . $e->getMessage());
                }
            }

            // 5. Create Payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'transaction_id' => 'TX-' . strtoupper(Str::random(10)),
                'payment_gateway' => $midtransServerKey ? 'Midtrans' : 'Internal Simulation',
                'snap_token' => $snapToken,
                'amount' => $total,
                'status' => 'pending',
                'paid_at' => null,
            ]);

            // 6. Save Order Coupon if applied
            if ($coupon) {
                OrderCoupon::create([
                    'order_id' => $order->id,
                    'coupon_id' => $coupon->id,
                    'discount' => $discount,
                ]);
            }

            // 7. Clear user cart
            CartItem::where('cart_id', $cart->id)->delete();

            // Log activity
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'checkout',
                    'model_type' => Order::class,
                    'model_id' => $order->id,
                    'description' => "Membuat pesanan baru: {$order->order_code} dengan total Rp " . number_format($order->total, 0, ',', '.'),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {}

            // 8. Notify user
            $user->notify(new OrderCreated($order));

            DB::commit();

            if ($redirectUrl) {
                return redirect($redirectUrl);
            }

            return redirect()->route('orders.show', $orderCode)->with('success', "Pesanan Anda #{$orderCode} berhasil dibuat! Silakan selesaikan pembayaran.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan Anda: ' . $e->getMessage());
        }
    }

    public function calculateShipping(Request $request)
    {
        $request->validate([
            'city_id' => 'required|integer',
            'weight' => 'required|integer',
            'courier' => 'required|string'
        ]);

        $apiKey = config('services.rajaongkir.api_key');
        $originCity = config('services.rajaongkir.origin_city_id', 152);

        if (!$apiKey) {
            return response()->json(['error' => 'API Key not configured'], 500);
        }

        try {
            $selectedCourier = 'jne';
            if (str_contains(strtolower($request->courier), 'pos')) {
                $selectedCourier = 'pos';
            } elseif (str_contains(strtolower($request->courier), 'jnt')) {
                $selectedCourier = 'tiki';
            }

            $response = Http::withoutVerifying()->timeout(5)->withHeaders([
                'key' => $apiKey
            ])->post('https://api.rajaongkir.com/starter/cost', [
                'origin' => (int) $originCity,
                'destination' => (int) $request->city_id,
                'weight' => (int) $request->weight,
                'courier' => $selectedCourier
            ]);
            
            if ($response->successful()) {
                return response()->json($response->json('rajaongkir.results.0.costs'));
            }
            return response()->json(['error' => 'RajaOngkir API Error: ' . $response->body()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

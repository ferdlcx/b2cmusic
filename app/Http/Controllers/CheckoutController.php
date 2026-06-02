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

        // Calculate shipping cost using RajaOngkir cost API if key is set
        $shippingCost = 25000; // default flat rate
        $apiKey = config('services.rajaongkir.api_key');
        $originCity = config('services.rajaongkir.origin_city_id', 152); // default Jakarta Barat
        
        if ($apiKey && $defaultAddress && $defaultAddress->city_id) {
            try {
                // Calculate total weight of cart
                $totalWeight = $cartItems->sum(function($item) {
                    return ($item->product->weight ?: 1000) * $item->quantity;
                });
                
                $response = Http::withHeaders([
                    'key' => $apiKey
                ])->post('https://api.rajaongkir.com/starter/cost', [
                    'origin' => (int) $originCity,
                    'destination' => (int) $defaultAddress->city_id,
                    'weight' => (int) $totalWeight,
                    'courier' => 'jne' // default courier for estimation
                ]);
                
                if ($response->successful()) {
                    $results = $response->json('rajaongkir.results.0.costs');
                    if ($results && count($results) > 0) {
                        $costVal = null;
                        foreach ($results as $c) {
                            if (str_contains(strtoupper($c['service']), 'REG')) {
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
                logger()->error('RajaOngkir Cost API failed in index: ' . $e->getMessage());
            }
        }

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

        // Calculate shipping cost using RajaOngkir cost API if key is set
        $shippingCost = $request->courier === 'JNE_YES' ? 40000 : 25000; // default flat rate
        $apiKey = config('services.rajaongkir.api_key');
        $originCity = config('services.rajaongkir.origin_city_id', 152); // default Jakarta Barat
        
        // Calculate total weight of cart
        $totalWeight = $cartItems->sum(function($item) {
            return ($item->product->weight ?: 1000) * $item->quantity;
        });

        $selectedCourier = 'jne';
        if (str_contains(strtolower($request->courier), 'pos')) {
            $selectedCourier = 'pos';
        }

        if ($apiKey && $address->city_id) {
            try {
                $response = Http::withHeaders([
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
                    
                    $payload = [
                        'transaction_details' => [
                            'order_id' => $orderCode,
                            'gross_amount' => (int) $total,
                        ],
                        'item_details' => $itemDetails,
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
                    ];
                    
                    $response = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->withBasicAuth($midtransServerKey, '')
                      ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $payload);
                      
                    if ($response->successful()) {
                        $snapToken = $response->json('token');
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

            DB::commit();

            return redirect()->route('orders.history')->with('success', "Pesanan Anda #{$orderCode} berhasil dibuat! Silakan lakukan pembayaran.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan Anda: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getUserCart()
    {
        $user = Auth::user();
        
        // Ensure cart exists
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        return $cart;
    }

    public function index()
    {
        $cart = $this->getUserCart();
        $cartItems = CartItem::with(['product.primaryImage', 'product.category'])
            ->where('cart_id', $cart->id)
            ->get();

        $subtotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Check stock
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        if ($request->input('action') === 'buy_now') {
            session(['buy_now' => [
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price
            ]]);
            return redirect()->route('checkout.index');
        }

        $cart = $this->getUserCart();

        // Check if item already exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($product->stock < $newQuantity) {
                return back()->with('error', 'Jumlah di keranjang melebihi stok yang tersedia.');
            }
            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price // Update price in case it changed
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang belanja.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem = CartItem::findOrFail($id);
        
        // Ensure this cart item belongs to the authenticated user
        $cart = $this->getUserCart();
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $product = $cartItem->product;
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi untuk jumlah ini.');
        }

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Keranjang belanja berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $cartItem = CartItem::find($id);
        
        if (!$cartItem) {
            return back();
        }

        $cart = $this->getUserCart();
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear()
    {
        $cart = $this->getUserCart();
        CartItem::where('cart_id', $cart->id)->delete();

        return back()->with('success', 'Keranjang belanja berhasil dikosongkan.');
    }
}

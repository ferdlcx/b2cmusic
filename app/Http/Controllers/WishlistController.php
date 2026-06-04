<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    private function getUserWishlist()
    {
        $user = Auth::user();
        return Wishlist::firstOrCreate(['user_id' => $user->id]);
    }

    public function index()
    {
        $wishlist = $this->getUserWishlist();
        $wishlistItems = WishlistItem::with(['product.primaryImage', 'product.category'])
            ->where('wishlist_id', $wishlist->id)
            ->get();

        return view('customer.wishlist', compact('wishlistItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $wishlist = $this->getUserWishlist();

        // Check if already in wishlist
        $exists = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if (!$exists) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $request->product_id,
            ]);
            return back()->with('success', 'Produk berhasil ditambahkan ke wishlist.');
        }

        return back()->with('info', 'Produk sudah ada di wishlist Anda.');
    }

    public function destroy($id)
    {
        $wishlist = $this->getUserWishlist();
        $item = WishlistItem::where('wishlist_id', $wishlist->id)->findOrFail($id);
        $item->delete();

        return back()->with('success', 'Produk berhasil dihapus dari wishlist.');
    }

    public function moveToCart($id)
    {
        $wishlist = $this->getUserWishlist();
        $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)->findOrFail($id);
        $product = $wishlistItem->product;

        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        // Check stock
        if ($product->stock < 1) {
            return back()->with('error', 'Stok produk habis, tidak dapat memindahkan ke keranjang.');
        }

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Check if item already exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + 1;
            if ($product->stock < $newQuantity) {
                return back()->with('error', 'Jumlah di keranjang melebihi stok yang tersedia.');
            }
            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $product->price,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'price' => $product->price,
                'quantity' => 1,
            ]);
        }

        // Remove from wishlist
        $wishlistItem->delete();

        return redirect()->route('cart.index')->with('success', 'Produk berhasil dipindahkan ke keranjang.');
    }
}

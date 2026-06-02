<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['paid', 'shipped', 'completed'])->sum('total');
        $totalProducts = Product::count();
        $pendingOrdersCount = Order::where('status', 'pending')->count();

        // Fetch recent orders
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders', 
            'totalRevenue', 
            'totalProducts', 
            'pendingOrdersCount', 
            'recentOrders'
        ));
    }

    // --- PRODUCTS CRUD ---
    public function products()
    {
        $products = Product::with(['category', 'primaryImage'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        $categories = Category::where('status', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'unique:products,sku'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            // Specifications
            'spec_names' => ['nullable', 'array'],
            'spec_values' => ['nullable', 'array'],
        ]);

        DB::beginTransaction();
        try {
            // Create Product
            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'category_id' => $request->category_id,
                'brand' => $request->brand,
                'price' => $request->price,
                'stock' => $request->stock,
                'sku' => $request->sku,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'status' => true,
            ]);

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $imageUrl = Storage::url($path);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageUrl,
                    'is_primary' => true,
                ]);
            }

            // Handle Specifications
            if ($request->spec_names && $request->spec_values) {
                foreach ($request->spec_names as $index => $name) {
                    if (!empty($name) && !empty($request->spec_values[$index])) {
                        ProductSpecification::create([
                            'product_id' => $product->id,
                            'spec_name' => $name,
                            'spec_value' => $request->spec_values[$index],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function editProduct($id)
    {
        $product = Product::with(['specifications', 'primaryImage'])->findOrFail($id);
        $categories = Category::where('status', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'unique:products,sku,' . $product->id],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            // Specifications
            'spec_names' => ['nullable', 'array'],
            'spec_values' => ['nullable', 'array'],
        ]);

        DB::beginTransaction();
        try {
            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'category_id' => $request->category_id,
                'brand' => $request->brand,
                'price' => $request->price,
                'stock' => $request->stock,
                'sku' => $request->sku,
                'short_description' => $request->short_description,
                'description' => $request->description,
            ]);

            // Handle New Image Upload
            if ($request->hasFile('image')) {
                // Delete old primary image if exists
                $oldPrimary = ProductImage::where('product_id', $product->id)->where('is_primary', true)->first();
                if ($oldPrimary) {
                    $oldPath = str_replace('/storage/', '', $oldPrimary->image);
                    Storage::disk('public')->delete($oldPath);
                    $oldPrimary->delete();
                }

                $path = $request->file('image')->store('products', 'public');
                $imageUrl = Storage::url($path);

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageUrl,
                    'is_primary' => true,
                ]);
            }

            // Sync Specifications (Delete old, insert new)
            ProductSpecification::where('product_id', $product->id)->delete();
            if ($request->spec_names && $request->spec_values) {
                foreach ($request->spec_names as $index => $name) {
                    if (!empty($name) && !empty($request->spec_values[$index])) {
                        ProductSpecification::create([
                            'product_id' => $product->id,
                            'spec_name' => $name,
                            'spec_value' => $request->spec_values[$index],
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete product images files from storage
            $images = ProductImage::where('product_id', $product->id)->get();
            foreach ($images as $img) {
                if (str_starts_with($img->image, '/storage/')) {
                    $path = str_replace('/storage/', '', $img->image);
                    Storage::disk('public')->delete($path);
                }
            }

            $product->delete(); // Cascades on migrations will handle DB deletions

            DB::commit();
            return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // --- ORDERS MANAGEMENT ---
    public function orders()
    {
        $orders = Order::with(['user', 'payment'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['user', 'address', 'items', 'payment', 'shipment'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => ['required', 'in:pending,paid,shipped,completed,canceled'],
            'tracking_number' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $order->update(['status' => $request->status]);

            // Update Shipment or Payment status accordingly
            $shipment = Shipment::where('order_id', $order->id)->first();
            if ($shipment) {
                $shipmentData = [];
                if ($request->tracking_number) {
                    $shipmentData['tracking_number'] = $request->tracking_number;
                }

                if ($request->status === 'shipped') {
                    $shipmentData['status'] = 'shipped';
                    $shipmentData['shipped_at'] = now();
                } elseif ($request->status === 'completed') {
                    $shipmentData['status'] = 'delivered';
                    $shipmentData['delivered_at'] = now();
                } elseif ($request->status === 'canceled') {
                    $shipmentData['status'] = 'canceled';
                }

                $shipment->update($shipmentData);
            }

            // Update payment status if marked as paid
            $payment = $order->payment;
            if ($payment && $request->status === 'paid' && $payment->status !== 'paid') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Status pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

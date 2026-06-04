<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Shipment;
use App\Models\Brand;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\ReturnRequest;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PaymentSuccess;
use App\Notifications\OrderShipped;
use App\Notifications\OrderCompleted;

class AdminController extends Controller
{
    private function logActivity($action, $modelType = null, $modelId = null, $description = null)
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently ignore activity logging errors to prevent breaking user actions
        }
    }

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
        $products = Product::with(['category', 'primaryImage', 'brand'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'condition' => ['required', 'in:new,used,refurbished'],
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
                'brand_id' => $request->brand_id,
                'condition' => $request->condition,
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

            $this->logActivity('create_product', Product::class, $product->id, "Menambahkan produk baru: {$product->name}");

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
        $brands = Brand::where('status', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'condition' => ['required', 'in:new,used,refurbished'],
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
                'brand_id' => $request->brand_id,
                'condition' => $request->condition,
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

            // Sync Specifications
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

            $this->logActivity('update_product', Product::class, $product->id, "Memperbarui produk: {$product->name}");

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
        $product->delete(); // Soft delete

        $this->logActivity('delete_product', Product::class, $product->id, "Menghapus produk (soft delete): {$product->name}");

        return redirect()->route('admin.products')->with('success', 'Produk berhasil dipindahkan ke tempat sampah.');
    }

    public function trashedProducts()
    {
        $products = Product::onlyTrashed()->with(['category', 'primaryImage', 'brand'])->orderBy('deleted_at', 'desc')->paginate(10);
        return view('admin.products.trashed', compact('products'));
    }

    public function restoreProduct($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        $this->logActivity('restore_product', Product::class, $product->id, "Memulihkan produk: {$product->name}");

        return redirect()->route('admin.products.trashed')->with('success', 'Produk berhasil dipulihkan.');
    }

    // --- CATEGORIES CRUD ---
    public function categories()
    {
        $categories = Category::with(['parent'])->withCount('products')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $imagePath = Storage::url($imagePath);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        $this->logActivity('create_category', Category::class, $category->id, "Menambahkan kategori baru: {$category->name}");

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $id],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                $oldPath = str_replace('/storage/', '', $category->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('categories', 'public');
            $imagePath = Storage::url($path);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        $this->logActivity('update_category', Category::class, $category->id, "Memperbarui kategori: {$category->name}");

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        
        // Delete image
        if ($category->image) {
            $oldPath = str_replace('/storage/', '', $category->image);
            Storage::disk('public')->delete($oldPath);
        }

        $category->delete();

        $this->logActivity('delete_category', Category::class, $id, "Menghapus kategori: {$category->name}");

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil dihapus.');
    }

    // --- BRANDS CRUD ---
    public function brands()
    {
        $brands = Brand::withCount('products')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function createBrand()
    {
        return view('admin.brands.create');
    }

    public function storeBrand(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $logoPath = Storage::url($path);
        }

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'logo' => $logoPath,
            'status' => $request->status,
        ]);

        $this->logActivity('create_brand', Brand::class, $brand->id, "Menambahkan brand baru: {$brand->name}");

        return redirect()->route('admin.brands')->with('success', 'Brand berhasil ditambahkan!');
    }

    public function editBrand($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function updateBrand(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name,' . $id],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = $brand->logo;
        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                $oldPath = str_replace('/storage/', '', $brand->logo);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('logo')->store('brands', 'public');
            $logoPath = Storage::url($path);
        }

        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'logo' => $logoPath,
            'status' => $request->status,
        ]);

        $this->logActivity('update_brand', Brand::class, $brand->id, "Memperbarui brand: {$brand->name}");

        return redirect()->route('admin.brands')->with('success', 'Brand berhasil diperbarui!');
    }

    public function destroyBrand($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->logo) {
            $oldPath = str_replace('/storage/', '', $brand->logo);
            Storage::disk('public')->delete($oldPath);
        }

        $brand->delete();

        $this->logActivity('delete_brand', Brand::class, $id, "Menghapus brand: {$brand->name}");

        return redirect()->route('admin.brands')->with('success', 'Brand berhasil dihapus.');
    }

    // --- USERS MANAGEMENT ---
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function showUser($id)
    {
        $user = User::with(['addresses', 'orders.payment'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Status akun Admin tidak dapat diubah.');
        }

        // Handle string statuses 'active' / 'inactive' or booleans
        $currentStatus = strtolower($user->status);
        if ($currentStatus === 'active' || $user->status == '1') {
            $newStatus = 'inactive';
        } else {
            $newStatus = 'active';
        }

        $user->update(['status' => $newStatus]);

        $this->logActivity('toggle_user_status', User::class, $user->id, "Mengubah status user {$user->name} menjadi {$newStatus}");

        return back()->with('success', 'Status pengguna berhasil diperbarui!');
    }

    // --- COUPONS CRUD ---
    public function coupons()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function createCoupon()
    {
        return view('admin.coupons.create');
    }

    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'type' => ['required', 'in:fixed,percent'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'boolean'],
        ]);

        $coupon = Coupon::create($request->all());

        $this->logActivity('create_coupon', Coupon::class, $coupon->id, "Menambahkan kupon baru: {$coupon->code}");

        return redirect()->route('admin.coupons')->with('success', 'Kupon berhasil ditambahkan!');
    }

    public function editCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function updateCoupon(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code,' . $id],
            'type' => ['required', 'in:fixed,percent'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'boolean'],
        ]);

        $coupon->update($request->all());

        $this->logActivity('update_coupon', Coupon::class, $coupon->id, "Memperbarui kupon: {$coupon->code}");

        return redirect()->route('admin.coupons')->with('success', 'Kupon berhasil diperbarui!');
    }

    public function destroyCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        $this->logActivity('delete_coupon', Coupon::class, $id, "Menghapus kupon: {$coupon->code}");

        return redirect()->route('admin.coupons')->with('success', 'Kupon berhasil dihapus.');
    }

    // --- REVIEWS MODERATION ---
    public function reviews()
    {
        $reviews = Review::with(['product', 'user'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function approveReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'approved']);

        $this->logActivity('approve_review', Review::class, $review->id, "Menyetujui ulasan dari {$review->user->name} untuk produk {$review->product->name}");

        return back()->with('success', 'Ulasan disetujui dan akan ditampilkan di halaman produk.');
    }

    public function rejectReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['status' => 'rejected']);

        $this->logActivity('reject_review', Review::class, $review->id, "Menolak ulasan dari {$review->user->name} untuk produk {$review->product->name}");

        return back()->with('success', 'Ulasan ditolak.');
    }

    // --- RETURN REQUESTS MANAGEMENT ---
    public function returnRequests()
    {
        $returns = ReturnRequest::with(['order', 'user'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.returns.index', compact('returns'));
    }

    public function approveReturn(Request $request, $id)
    {
        $return = ReturnRequest::with('order')->findOrFail($id);
        
        $request->validate([
            'admin_notes' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $return->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes
            ]);

            // Update corresponding order status
            $return->order->update(['status' => 'returned']);

            $this->logActivity('approve_return', ReturnRequest::class, $return->id, "Menyetujui pengembalian barang pesanan {$return->order->order_code}");

            DB::commit();
            return back()->with('success', 'Pengembalian barang berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rejectReturn(Request $request, $id)
    {
        $return = ReturnRequest::with('order')->findOrFail($id);

        $request->validate([
            'admin_notes' => ['required', 'string'], // Admin must provide reason for rejection
        ]);

        $return->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes
        ]);

        $this->logActivity('reject_return', ReturnRequest::class, $return->id, "Menolak pengembalian barang pesanan {$return->order->order_code}");

        return back()->with('success', 'Pengembalian barang ditolak.');
    }

    // --- FLASH SALES CRUD ---
    public function flashSales()
    {
        $flashSales = FlashSale::withCount('items')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.flash_sales.index', compact('flashSales'));
    }

    public function createFlashSale()
    {
        $products = Product::where('status', true)->get();
        return view('admin.flash_sales.create', compact('products'));
    }

    public function storeFlashSale(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'status' => ['required', 'boolean'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['required', 'exists:products,id'],
            'discount_prices' => ['required', 'array'],
            'discount_prices.*' => ['required', 'numeric', 'min:0'],
            'stocks' => ['required', 'array'],
            'stocks.*' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();
        try {
            $flashSale = FlashSale::create([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
            ]);

            foreach ($request->products as $index => $productId) {
                FlashSaleItem::create([
                    'flash_sale_id' => $flashSale->id,
                    'product_id' => $productId,
                    'discount_price' => $request->discount_prices[$index],
                    'stock' => $request->stocks[$index],
                    'sold' => 0,
                ]);
            }

            $this->logActivity('create_flash_sale', FlashSale::class, $flashSale->id, "Membuat flash sale baru: {$flashSale->name}");

            DB::commit();
            return redirect()->route('admin.flashSales')->with('success', 'Flash Sale berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function editFlashSale($id)
    {
        $flashSale = FlashSale::with('items.product')->findOrFail($id);
        $products = Product::where('status', true)->get();
        return view('admin.flash_sales.edit', compact('flashSale', 'products'));
    }

    public function updateFlashSale(Request $request, $id)
    {
        $flashSale = FlashSale::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'status' => ['required', 'boolean'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['required', 'exists:products,id'],
            'discount_prices' => ['required', 'array'],
            'discount_prices.*' => ['required', 'numeric', 'min:0'],
            'stocks' => ['required', 'array'],
            'stocks.*' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();
        try {
            $flashSale->update([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
            ]);

            // Sync: delete old items and write new ones
            FlashSaleItem::where('flash_sale_id', $flashSale->id)->delete();

            foreach ($request->products as $index => $productId) {
                FlashSaleItem::create([
                    'flash_sale_id' => $flashSale->id,
                    'product_id' => $productId,
                    'discount_price' => $request->discount_prices[$index],
                    'stock' => $request->stocks[$index],
                    'sold' => 0,
                ]);
            }

            $this->logActivity('update_flash_sale', FlashSale::class, $flashSale->id, "Memperbarui flash sale: {$flashSale->name}");

            DB::commit();
            return redirect()->route('admin.flashSales')->with('success', 'Flash Sale berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyFlashSale($id)
    {
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->delete(); // Cascades on DB schema

        $this->logActivity('delete_flash_sale', FlashSale::class, $id, "Menghapus flash sale: {$flashSale->name}");

        return redirect()->route('admin.flashSales')->with('success', 'Flash Sale berhasil dihapus.');
    }

    // --- ACTIVITY LOGS ---
    public function activityLog()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.activity_log', compact('logs'));
    }

    // --- ORDERS MANAGEMENT ---
    public function orders()
    {
        $orders = Order::with(['user', 'payment'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['user', 'address', 'items.product.primaryImage', 'payment', 'shipment'])->findOrFail($id);
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

            // Trigger database notifications for user
            if ($request->status === 'paid') {
                $order->user->notify(new PaymentSuccess($order));
            } elseif ($request->status === 'shipped') {
                $order->user->notify(new OrderShipped($order));
            } elseif ($request->status === 'completed') {
                $order->user->notify(new OrderCompleted($order));
            }

            $this->logActivity('update_order_status', Order::class, $order->id, "Mengubah status pesanan {$order->order_code} menjadi {$request->status}");

            DB::commit();
            return back()->with('success', 'Status pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

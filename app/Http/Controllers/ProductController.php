<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function home()
    {
        $categories = Category::where('status', true)->limit(6)->get();
        $products = Product::with(['category', 'primaryImage'])
            ->where('status', true)
            ->limit(3)
            ->get();
        return view('landing', compact('categories', 'products'));
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage'])->where('status', true);

        // Filter by Category
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search by Keyword
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('brand', function($b) use ($search) {
                      $b->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by Brand
        if ($request->has('brand') && is_array($request->brand)) {
            $query->whereIn('brand_id', $request->brand);
        }

        // Filter by Price Range
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by Rating (requires joining reviews or using withAvg)
        if ($request->has('rating') && $request->rating != '') {
            $minRating = $request->rating;
            // Since we need to filter by average rating, we can use withAvg
            $query->withAvg('reviews', 'rating')
                  ->having('reviews_avg_rating', '>=', $minRating);
        }

        // Sort
        if ($request->has('sort')) {
            if ($request->sort == 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort == 'newest') {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();

        return view('products.catalog', compact('products', 'categories', 'brands'));
    }

    public function show($slug)
    {
        $product = Product::with([
            'category', 
            'images', 
            'videos', 
            'specifications', 
            'reviews.user'
        ])
        ->where('slug', $slug)
        ->where('status', true)
        ->firstOrFail();

        // Calculate average rating
        $averageRating = $product->reviews->avg('rating') ?: 0;

        // Fetch related products
        $relatedProducts = Product::with(['category', 'primaryImage'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', true)
            ->limit(3)
            ->get();

        return view('products.show', compact('product', 'averageRating', 'relatedProducts'));
    }
}

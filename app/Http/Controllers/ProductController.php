<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
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

        return view('products.catalog', compact('products', 'categories'));
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

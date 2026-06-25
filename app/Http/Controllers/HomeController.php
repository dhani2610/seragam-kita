<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $sliders = Slider::all();
        $categories = Category::all();
        
        // Latest products (limit 8)
        $latestProducts = Product::with(['images', 'reviews'])->orderBy('created_at', 'desc')->limit(8)->get();

        // Search product logic
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $query = Product::with(['images', 'reviews']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('home.index', compact('sliders', 'categories', 'latestProducts', 'products', 'search', 'categoryId'));
    }

    public function showProduct($slug)
    {
        $product = Product::with(['images', 'variants', 'reviews.user'])->where('slug', $slug)->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)->get();
            
        return view('home.product_detail', compact('product', 'relatedProducts'));
    }

    public function aboutUs()
    {
        $about = Setting::getValue('about_us', 'Tentang Kami');
        return view('home.about', compact('about'));
    }

    public function flow()
    {
        return view('home.flow');
    }

    // Customer Dashboard / History
    public function dashboard()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'items.variant'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('home.dashboard', compact('orders'));
    }

    // Submit Review / Rating
    public function submitReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Check if user has bought this item first
        $hasPurchased = Order::where('user_id', Auth::id())
            ->where('payment_status', 'Paid')
            ->whereHas('items', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })->exists();

        if (!$hasPurchased) {
            return response()->json([
                'success' => false,
                'message' => 'Anda hanya dapat memberikan ulasan pada produk yang telah Anda beli!'
            ], 403);
        }

        Review::updateOrCreate(
            [
                'product_id' => $request->product_id,
                'user_id' => Auth::id()
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Ulasan Anda berhasil dikirim!'
        ]);
    }
}

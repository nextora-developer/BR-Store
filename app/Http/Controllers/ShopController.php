<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Voucher;
use App\Models\Banner;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // Homepage
    public function home()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id') // ✅ 只拿 parent
            ->with(['children' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $featured = Product::where('is_active', true)
            ->latest()
            ->limit(10)
            ->get();

        $homeVouchers = Voucher::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->take(2)
            ->get();

        return view('shop.home', compact('featured', 'categories', 'banners', 'homeVouchers'));
    }

    // Shop listing + Search
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($search = $request->q) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($categorySlug = $request->category) {
            $cat = Category::where('slug', $categorySlug)
                ->where('is_active', true)
                ->first();

            if ($cat) {
                if (is_null($cat->parent_id)) {
                    // ✅ parent：筛选它所有 sub 的 products
                    $subIds = Category::where('parent_id', $cat->id)
                        ->where('is_active', true)
                        ->pluck('id');

                    // 没有 sub 就返回空（避免 parent 误显示全部产品）
                    $query->whereIn('category_id', $subIds->all());
                } else {
                    // ✅ sub：直接筛
                    $query->where('category_id', $cat->id);
                }
            }
        }

        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
                $query->latest();
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(15);

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->orderBy('name');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        return view('shop.index', compact('products', 'categories'));
    }

    // Product detail (route model binding by slug already in your routes)
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'category',
            'options.values' => fn($q) => $q->orderBy('sort_order'),
            'variants' => fn($q) => $q->where('is_active', true),
        ]);

        $reviews = $product->reviews()
            ->with('user:id,name')
            ->latest()
            ->paginate(6);

        $avgRating = round($product->reviews()->avg('rating') ?? 0, 1);
        $reviewCount = $product->reviews()->count();


        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true) // ✅ 只拿 active
            ->latest()
            ->limit(4)
            ->get();


        return view('shop.show', compact('product','related','reviews','avgRating','reviewCount'));
    }
}

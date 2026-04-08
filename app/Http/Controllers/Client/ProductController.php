<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review; 
use App\Models\Order;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 

class ProductController extends Controller
{
    public function show(Request $request, $slug)
    {
        $product = Product::with(['images', 'category'])->where('slug', $slug)->firstOrFail();

        $colors = DB::table('attribute_values')
            ->join('variant_attribute_values', 'attribute_values.id', '=', 'variant_attribute_values.attribute_value_id')
            ->join('product_variants', 'variant_attribute_values.variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $product->id)
            ->where('attribute_values.attribute_id', 3)
            ->select('attribute_values.id', 'attribute_values.value', 'attribute_values.color_code')
            ->distinct()->get();

        $sizes = DB::table('attribute_values')
            ->join('variant_attribute_values', 'attribute_values.id', '=', 'variant_attribute_values.attribute_value_id')
            ->join('product_variants', 'variant_attribute_values.variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $product->id)
            ->where('attribute_values.attribute_id', 4)
            ->select('attribute_values.id', 'attribute_values.value')
            ->distinct()->get();

        $variantData = [];
        $variants = DB::table('product_variants')->where('product_id', $product->id)->get();
        foreach($variants as $v) {
            $attrs = DB::table('variant_attribute_values')->where('variant_id', $v->id)->pluck('attribute_value_id')->toArray();
            sort($attrs);
            $key = empty($attrs) ? 'default' : implode('_', $attrs);
            $variantData[$key] = [
                'id' => $v->id, 
                'price' => number_format($v->price) . 'đ',
                'sale_price' => $v->sale_price ? number_format($v->sale_price) . 'đ' : null,
                'stock' => (int)$v->stock
            ];
        }

        $relatedProducts = Product::with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)->get();

        // ========================================================
        // 6. LOGIC ĐÁNH GIÁ (FIX N+1 QUERY VÀ TỐI ƯU RAM)
        // ========================================================
        
        // A. Thống kê siêu tốc bằng Database (Không dùng ->get() để tránh tràn RAM)
        $totalReviews = Review::where('product_id', $product->id)->approved()->count();
        $avgRating = $totalReviews > 0 ? round(Review::where('product_id', $product->id)->approved()->avg('rating'), 1) : 0;
        
        // Fix Lỗi 1: Đếm số đánh giá có ảnh bằng hàm has() của SQL (Không bị N+1)
        $imageCount = Review::where('product_id', $product->id)->approved()->has('images')->count();

        // Fix Lỗi 5: Đếm số lượng từng loại sao bằng selectRaw & groupBy
        $ratingCounts = Review::where('product_id', $product->id)->approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        $starCounts = [
            5 => $ratingCounts[5] ?? 0,
            4 => $ratingCounts[4] ?? 0,
            3 => $ratingCounts[3] ?? 0,
            2 => $ratingCounts[2] ?? 0,
            1 => $ratingCounts[1] ?? 0,
        ];

        // B. Lọc và Phân Trang
        $query = Review::where('product_id', $product->id)->approved()->latest()->with(['user', 'images', 'reply']);
        $currentFilter = $request->input('filter', 'all');
        
        if ($currentFilter !== 'all') {
            if ($currentFilter === 'image') {
                $query->has('images');
            } else {
                $query->where('rating', (int)$currentFilter);
            }
        }
        $paginatedReviews = $query->paginate(5, ['*'], 'review_page')->appends($request->query())->fragment('reviews');

        // C. Kiểm tra quyền Đánh giá
        $canReview = false;
        if(Auth::check()) {
            $userId = Auth::id();
            $canReview = Order::where('user_id', $userId)
                ->where('order_status', 'completed')
                ->whereHas('items', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->whereNotIn('id', function($q) use ($userId, $product) {
                    $q->select('order_id')->from('reviews')->where('user_id', $userId)->where('product_id', $product->id);
                })
                ->exists();
        }

        return view('client.product-detail', compact(
            'product', 'colors', 'sizes', 'variantData', 'relatedProducts',
            'totalReviews', 'avgRating', 'starCounts', 'imageCount',        
            'currentFilter', 'paginatedReviews', 'canReview'                
        ));
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(\Illuminate\Http\Request $request, $slug)
    {
        // 1. Tìm danh mục
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();

        // 2. Gom ID danh mục cha - con
        if (is_null($category->parent_id)) {
            $categoryIds = $category->children->pluck('id')->toArray();
            $categoryIds[] = $category->id;
        } else {
            $categoryIds = [$category->id];
        }

        // 3. Khởi tạo câu Query tìm sản phẩm
        $query = \App\Models\Product::whereIn('category_id', $categoryIds)
                           ->with(['images', 'variants']);

        // 🚀 THUẬT TOÁN BỘ LỌC (SẮP XẾP) Ở ĐÂY
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    // COALESCE: Xếp theo giá sale, nếu không có sale thì lấy giá gốc
                    $query->orderByRaw('COALESCE(sale_price, price) ASC');
                    break;
                case 'price_desc':
                    $query->orderByRaw('COALESCE(sale_price, price) DESC');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'ASC');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'DESC');
                    break;
                case 'stock_desc':
                    $query->orderBy('stock', 'DESC');
                    break;
            }
        } else {
            // Mặc định nếu không lọc thì xếp sản phẩm mới nhất lên đầu
            $query->latest(); 
        }

        // 4. Phân trang & Giữ lại biến ?sort trên thanh URL khi khách bấm sang trang 2, 3...
        $products = $query->paginate(12)->withQueryString();

        return view('client.category', compact('category', 'products'));
    }
}
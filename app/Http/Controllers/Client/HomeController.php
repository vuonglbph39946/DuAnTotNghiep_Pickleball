<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. LẤY DANH SÁCH BANNER (Chỉ lấy những cái đang hiển thị, sắp xếp theo position)
        $banners = Banner::where('status', 1)->orderBy('position', 'asc')->orderBy('id', 'desc')->get();

        // 2. LẤY CÁC DANH MỤC GỐC
        $categories = Category::whereNull('parent_id')
            ->where('status', 1)
            ->get();

        // 3. LẤY SẢN PHẨM CHO TỪNG DANH MỤC
        // 3. LẤY SẢN PHẨM CHO TỪNG DANH MỤC
        foreach ($categories as $category) {
            $categoryIds = Category::where('parent_id', $category->id)
                ->pluck('id')
                ->push($category->id);

            // Bổ sung 'variants' vào hàm with() để lấy số lượng kích thước
            $category->home_products = Product::with(['images', 'variants'])
                ->whereIn('category_id', $categoryIds)
                ->where('status', 1) 
                ->latest()
                ->take(8) 
                ->get();
        }

        // Truyền cả Banners, Categories và Products ra ngoài View
        return view('client.home', compact('banners', 'categories'));
    }

    public function searchSuggest(Request $request)
{
    $keyword = $request->keyword;
    
    // Tìm các sản phẩm có tên chứa keyword
    $products = \App\Models\Product::where('name', 'LIKE', '%' . $keyword . '%')
                    ->where('status', 1)
                    ->take(5) // Gợi ý 5 sản phẩm thôi cho nhanh
                    ->get();
    
    $results = [];
    foreach($products as $p) {
        $results[] = [
            'name' => $p->name,
            'price' => number_format($p->sale_price ?? $p->price) . 'đ',
            'image' => asset($p->images->first()->image_path ?? 'client/images/default.jpg'),
            'url' => url('product/' . $p->slug)
        ];
    }
    
    return response()->json($results);
}

public function search(Request $request)
    {
        // Lấy từ khóa khách hàng vừa nhập
        $keyword = $request->keyword;

        // Nếu khách không nhập gì mà bấm tìm kiếm -> trả về trang chủ
        if(empty($keyword)) {
            return redirect()->route('home');
        }

        // Tìm sản phẩm giống với từ khóa (có phân trang)
        $products = \App\Models\Product::where('name', 'LIKE', '%' . $keyword . '%')
                                       ->where('status', 1)
                                       ->paginate(12);

        // Nối thêm keyword vào link phân trang để khi bấm sang trang 2 không bị mất từ khóa
        $products->appends(['keyword' => $keyword]);

        return view('client.search', compact('products', 'keyword'));
    }
}
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
        foreach ($categories as $category) {
            $categoryIds = Category::where('parent_id', $category->id)
                ->pluck('id')
                ->push($category->id);

            // Truy vấn lấy 8 sản phẩm mới nhất
            $category->home_products = Product::with('images')
                ->whereIn('category_id', $categoryIds)
                ->where('status', 1) 
                ->latest()
                ->take(8) 
                ->get();
        }

        // Truyền cả Banners, Categories và Products ra ngoài View
        return view('client.home', compact('banners', 'categories'));
    }
}
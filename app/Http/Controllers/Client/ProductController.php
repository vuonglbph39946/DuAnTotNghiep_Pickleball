<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($slug)
    {
        // 1. Lấy thông tin sản phẩm cùng với ảnh và danh mục
        $product = Product::with(['images', 'category'])->where('slug', $slug)->firstOrFail();

        // 2. Lấy danh sách Màu sắc của sản phẩm này
        $colors = DB::table('attribute_values')
            ->join('variant_attribute_values', 'attribute_values.id', '=', 'variant_attribute_values.attribute_value_id')
            ->join('product_variants', 'variant_attribute_values.variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $product->id)
            ->where('attribute_values.attribute_id', 3)
            ->select('attribute_values.id', 'attribute_values.value', 'attribute_values.color_code')
            ->distinct()->get();

        // 3. Lấy danh sách Kích thước
        $sizes = DB::table('attribute_values')
            ->join('variant_attribute_values', 'attribute_values.id', '=', 'variant_attribute_values.attribute_value_id')
            ->join('product_variants', 'variant_attribute_values.variant_id', '=', 'product_variants.id')
            ->where('product_variants.product_id', $product->id)
            ->where('attribute_values.attribute_id', 4)
            ->select('attribute_values.id', 'attribute_values.value')
            ->distinct()->get();

        // 4. Lấy Data Biến Thể để đổi Giá & Tồn Kho bằng JS
        $variantData = [];
        $variants = DB::table('product_variants')->where('product_id', $product->id)->get();
        foreach($variants as $v) {
            $attrs = DB::table('variant_attribute_values')->where('variant_id', $v->id)->pluck('attribute_value_id')->toArray();
            sort($attrs);
            $key = empty($attrs) ? 'default' : implode('_', $attrs);
            $variantData[$key] = [
                'price' => number_format($v->price) . 'đ',
                'sale_price' => $v->sale_price ? number_format($v->sale_price) . 'đ' : null,
                'stock' => (int)$v->stock
            ];
        }

        // 5. Lấy các sản phẩm liên quan (Cùng danh mục)
        $relatedProducts = Product::with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)->get();

        return view('client.product-detail', compact('product', 'colors', 'sizes', 'variantData', 'relatedProducts'));
    }
}
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    // ==========================================
    // 1. THÊM SẢN PHẨM VÀO GIỎ
    // ==========================================
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $qty = $request->quantity;

        $product = Product::where('id', $productId)->where('status', 1)->first();
        if (!$product) return response()->json(['error' => 'Sản phẩm không tồn tại!'], 404);

        $stock = 0; 
        $variantInfo = ''; 
        // ĐÃ FIX: Lấy đúng cột sale_price
        $price = $product->sale_price ?? $product->price; 
        
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if (!$variant) return response()->json(['error' => 'Phân loại không tồn tại!'], 404);
            
            $stock = $variant->stock;
            // ĐÃ FIX: Lấy giá sale của biến thể (nếu có)
            $price = $variant->sale_price ?? $variant->price; 

            // ĐÃ FIX: Truy vấn bảng trung gian để lấy tên Màu sắc / Kích thước không bị lỗi 500
            $attrs = DB::table('variant_attribute_values')
                        ->join('attribute_values', 'variant_attribute_values.attribute_value_id', '=', 'attribute_values.id')
                        ->where('variant_attribute_values.variant_id', $variantId)
                        ->pluck('attribute_values.value')
                        ->toArray();
            $variantInfo = implode(' - ', $attrs);
        } else {
            $stock = $product->stock;
        }

        $cartKey = $variantId ? $productId . '_' . $variantId : $productId . '_0';
        $cart = session()->get('cart', []);

        $currentQty = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        if (($currentQty + $qty) > $stock) {
            return response()->json(['error' => "Chỉ còn {$stock} sản phẩm trong kho."], 400);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $qty;
        } else {
            $cart[$cartKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'slug' => $product->slug, // ĐÃ FIX: Lưu slug để làm Link trỏ tới trang chi tiết
                'name' => $product->name,
                'variant_info' => $variantInfo,
                'quantity' => $qty,
                'price' => $price,
                'image' => $product->images->first()->image_path ?? 'client/images/product-01.jpg',
                'max_stock' => $stock
            ];
        }

        session()->put('cart', $cart);

        // ĐÃ SỬA: Tính tổng số lượng tất cả sản phẩm
        $totalQuantity = array_sum(array_column($cart, 'quantity'));

        return response()->json(['success' => true, 'cart_count' => $totalQuantity, 'msg' => 'Đã thêm vào giỏ hàng!']);
    }

    public function renderCart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) $total += $item['price'] * $item['quantity'];
        return view('client.partials.cart-drop', compact('cart', 'total'))->render();
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) $total += $item['price'] * $item['quantity'];
        return view('client.cart.index', compact('cart', 'total'));
    }

    public function update(Request $request)
    {
        $cartKey = $request->cart_key;
        $qty = $request->quantity;

        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) {
            if ($qty > $cart[$cartKey]['max_stock']) {
                return response()->json(['success' => false, 'msg' => "Chỉ còn tối đa {$cart[$cartKey]['max_stock']} sản phẩm."]);
            }
            $cart[$cartKey]['quantity'] = $qty;
            session()->put('cart', $cart);

            $itemTotal = $cart[$cartKey]['price'] * $qty;
            $total = 0;
            foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

            // ĐÃ SỬA: Tính tổng số lượng
            $totalQuantity = array_sum(array_column($cart, 'quantity'));

            return response()->json(['success' => true, 'cart_count' => $totalQuantity, 'item_total' => number_format($itemTotal).'đ', 'total' => number_format($total).'đ']);
        }
        return response()->json(['success' => false, 'msg' => 'Lỗi dữ liệu giỏ hàng!']);
    }

    public function remove(Request $request)
    {
        $cartKey = $request->cart_key;
        $cart = session()->get('cart', []);
        
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }

        $total = 0;
        foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

        // ĐÃ SỬA: Tính tổng số lượng
        $totalQuantity = array_sum(array_column($cart, 'quantity'));

        return response()->json(['success' => true, 'cart_count' => $totalQuantity, 'total' => number_format($total).'đ']);
    }
}
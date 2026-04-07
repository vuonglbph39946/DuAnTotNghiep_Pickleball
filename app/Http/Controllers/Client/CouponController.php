<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CouponService;
use App\Services\ShippingService; // Thêm Service mới
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    protected $couponService;
    protected $shippingService; // Khai báo biến

    // Tiêm (Inject) cả CouponService và ShippingService vào đây
    public function __construct(CouponService $couponService, ShippingService $shippingService)
    {
        $this->couponService = $couponService;
        $this->shippingService = $shippingService;
    }

    public function apply(Request $request)
    {
        // Yêu cầu phải có chuỗi gửi lên
        $request->validate(['coupon_code' => 'required|string']);
        
        // VÁ LỖI: Xóa khoảng trắng 2 đầu và ép in hoa để tránh lỗi gõ phím
        $code = strtoupper(trim($request->coupon_code));
        
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['success' => false, 'msg' => 'Giỏ hàng của bạn đang trống.']);
        }

        // Tính tổng tiền tạm tính
        $subTotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));

        // GỌI SERVICE: Chỉ truyền $code (đã xử lý) và $subTotal vào, KHÔNG cần $shippingFee
        $result = $this->couponService->validateAndCalculate($code, $subTotal, Auth::id(), false);

        if ($result['success']) {
            
            // === CẢI TIẾN 3: BẢO MẬT SESSION (Chỉ lưu code, không lưu tiền) ===
            session()->put('applied_coupon', [
                'code' => $result['code'],
                'applied_at' => now() // Gắn đồng hồ bấm giờ vào Session
            ]);

            // === CẢI TIẾN 1: GỌI TỪ SHIPPING SERVICE CHUẨN KIẾN TRÚC ===
            $shippingFee = $this->shippingService->calculateFee($subTotal);
            $newTotal = $subTotal + $shippingFee - $result['discount_amount'];

            return response()->json([
                'success' => true,
                'msg' => $result['msg'],
                'discount_amount' => number_format($result['discount_amount']) . 'đ',
                'new_total' => number_format(max(0, $newTotal)) . 'đ'
            ]);
        }

        return response()->json(['success' => false, 'msg' => $result['msg']]);
    }
}
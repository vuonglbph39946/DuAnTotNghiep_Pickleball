<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * @param string $code Mã giảm giá
     * @param float $subTotal Tổng tiền tạm tính của giỏ hàng
     * @param int|null $userId ID người dùng
     * @param bool $lock Khóa dòng DB (Dùng lúc Checkout để chống 2 user giành 1 mã cuối cùng)
     */
    public function validateAndCalculate($code, $subTotal, $userId = null, $lock = false)
    {
        // 1. Lấy mã giảm giá (Có Lock DB nếu đang ở bước Checkout)
        $query = Coupon::where('code', $code)->where('status', 1);
        if ($lock) {
            $query->lockForUpdate(); // Transaction Lock chống Race Condition
        }
        $coupon = $query->first();

        // 2. Validate Tồn tại & Trạng thái
        if (!$coupon) {
            return ['success' => false, 'msg' => 'Mã giảm giá không tồn tại hoặc đã bị khóa.'];
        }

        // 3. Validate Thời gian
        $now = now();
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return ['success' => false, 'msg' => 'Mã giảm giá chưa đến thời gian sử dụng.'];
        }
        
        // ĐÃ FIX: Thêm ->endOfDay() để tính đến 23:59:59 của ngày kết thúc
        if ($coupon->end_date && $now->gt($coupon->end_date->endOfDay())) {
            return ['success' => false, 'msg' => 'Mã giảm giá đã hết hạn.'];
        }

        // 4. Validate Số lượng
        if ($coupon->quantity <= 0) {
            return ['success' => false, 'msg' => 'Mã giảm giá đã hết lượt sử dụng.'];
        }

        // 5. Validate Giá trị tối thiểu
        if ($subTotal < $coupon->min_order_value) {
            return ['success' => false, 'msg' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon->min_order_value) . 'đ.'];
        }

        // === ĐÃ FIX LỖI 3: BẮT BUỘC ĐĂNG NHẬP ĐỂ CHỐNG HACK LƯỢT DÙNG ===
        if (!$userId) {
            return ['success' => false, 'msg' => 'Vui lòng đăng nhập tài khoản để sử dụng mã giảm giá!'];
        }

        // 6. Validate Lịch sử sử dụng của User (Tránh 1 user xài 1 mã nhiều lần)
        if ($userId) {
            $usageCount = DB::table('coupon_user')
                ->where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();

            if ($usageCount >= $coupon->max_usage_per_user) {
                return ['success' => false, 'msg' => 'Bạn đã sử dụng hết số lượt cho phép của mã này.'];
            }
        }
        
       // 7. Tính toán số tiền giảm
        $discountAmount = 0;
        if ($coupon->discount_type === 'percent') {
            $discountAmount = ($subTotal * $coupon->discount_value) / 100;
            
            // ĐÃ VÁ LỖI 1: Chỉ áp dụng max_discount nếu nó lớn hơn 0
            if (!is_null($coupon->max_discount_value) && $coupon->max_discount_value > 0 && $discountAmount > $coupon->max_discount_value) {
                $discountAmount = $coupon->max_discount_value;
            }
        } else { 
            $discountAmount = $coupon->discount_value;
        }

        // Đảm bảo tiền giảm không bao giờ lớn hơn tiền hàng (gây âm tiền)
        if ($discountAmount > $subTotal) {
            $discountAmount = $subTotal;
        }

        return [
            'success' => true,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount_amount' => $discountAmount,
            'msg' => 'Áp dụng mã giảm giá thành công!'
        ];
    }
}
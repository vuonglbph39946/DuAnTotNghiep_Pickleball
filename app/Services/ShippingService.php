<?php

namespace App\Services;

class ShippingService
{
    /**
     * Tính toán phí giao hàng dựa trên tổng tiền tạm tính
     * * @param float $subTotal Tổng tiền tạm tính của giỏ hàng
     * @return float Phí giao hàng
     */
    public function calculateFee($subTotal)
    {
        // Logic: Đơn hàng trên 500k thì Free ship, ngược lại thu 30k
        return $subTotal > 500000 ? 0 : 30000;
    }
}
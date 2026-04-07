<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    private function clearDashboardCache()
    {
        foreach ([0, 7, 30] as $days) {
            Cache::forget("dashboard_stats_{$days}");
        }
    }

    public function created(Order $order) { 
        $this->clearDashboardCache(); 
    }

    public function updated(Order $order) 
    { 
        // BỘ LỌC THÔNG MINH: Chỉ xóa Cache khi Trạng thái, Thanh toán hoặc Tiền thay đổi
        if ($order->wasChanged(['order_status', 'payment_status', 'total_amount', 'discount_amount'])) {
            $this->clearDashboardCache(); 
        }
    }

    public function deleted(Order $order) { 
        $this->clearDashboardCache(); 
    }
}
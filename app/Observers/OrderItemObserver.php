<?php

namespace App\Observers;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Cache;

class OrderItemObserver
{
    private function clearDashboardCache() 
    {
        foreach ([0, 7, 30] as $days) { 
            Cache::forget("dashboard_stats_{$days}"); 
        }
    }

    public function created(OrderItem $item) { 
        $this->clearDashboardCache(); 
    }

    public function updated(OrderItem $item) 
    { 
        // BỘ LỌC THÔNG MINH: Đổi số lượng vợt hay đổi giá thì mới phải xóa Cache để tính lại thống kê
        if ($item->wasChanged(['quantity', 'price'])) {
            $this->clearDashboardCache(); 
        }
    }

    public function deleted(OrderItem $item) { 
        $this->clearDashboardCache(); 
    }
}
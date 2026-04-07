<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatisticService
{
    public function getDashboardData($days = 7): array
    {
        // Lưu Cache 1 ngày (86400s), Observer sẽ tự động xóa khi có đơn mới
        return Cache::remember("dashboard_stats_{$days}", 86400, function () use ($days) {
            
            $baseOrderQuery = Order::query();
            $customerQuery = User::where('role', 'customer');
            $startDate = null;

            // VÁ LỖI 2.4: Xử lý logic thời gian chuẩn, không dùng subYears(10)
            if ($days > 0) {
                $startDate = Carbon::today()->subDays($days - 1);
                $baseOrderQuery->where('created_at', '>=', $startDate);
                $customerQuery->where('created_at', '>=', $startDate);
            }

            $totalOrders = (clone $baseOrderQuery)->count();
            $completedOrders = (clone $baseOrderQuery)->where('order_status', 'completed')->count();
            $cancelledOrders = (clone $baseOrderQuery)->where('order_status', 'cancelled')->count();

            $netRevenue = (clone $baseOrderQuery)
                ->where('order_status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $aov = $completedOrders > 0 ? round($netRevenue / $completedOrders) : 0;
            $cancelRate = $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 2) : 0;

            return [
                'net_revenue'    => $netRevenue,
                'cancel_rate'    => $cancelRate,
                'aov'            => $aov,
                'new_customers'  => $customerQuery->count(), // Nâng cấp 3.1
                'chart_data'     => $this->getRevenueChartOptimized($days),
                'monthly_chart'  => $this->getMonthlyRevenueChart(), // Nâng cấp 3.2
                'top_products'   => $this->getTop5Products($startDate),
                'top_categories' => $this->getTopCategories($startDate), // Nâng cấp 3.4
                'voucher_stats'  => $this->getVoucherStats($startDate),
            ];
        });
    }

    private function getRevenueChartOptimized($days): array
    {
        $chartDays = $days == 0 ? 30 : $days; 

        $chartQuery = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('order_status', 'completed')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::today()->subDays($chartDays - 1))
            ->groupBy('date')
            ->pluck('total', 'date'); 

        $labels = []; $data = [];
        for ($i = $chartDays - 1; $i >= 0; $i--) {
            $dateStr = Carbon::today()->subDays($i)->toDateString();
            $labels[] = Carbon::parse($dateStr)->format('d/m');
            $data[] = $chartQuery->has($dateStr) ? (float) $chartQuery[$dateStr] : 0;
        }
        return ['labels' => $labels, 'data' => $data];
    }

    // NÂNG CẤP 3.2: Biểu đồ doanh thu 6 tháng gần nhất (Bar Chart)
    private function getMonthlyRevenueChart(): array
    {
        $query = Order::selectRaw('DATE_FORMAT(created_at, "%m/%Y") as month_year, SUM(total_amount) as total')
            ->where('order_status', 'completed')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month_year')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'month_year');

        $labels = []; $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStr = Carbon::now()->subMonths($i)->format('m/Y');
            $labels[] = $monthStr;
            $data[] = $query->has($monthStr) ? (float) $query[$monthStr] : 0;
        }
        return ['labels' => $labels, 'data' => $data];
    }

    private function getTop5Products($startDate)
    {
        $query = OrderItem::select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.order_status', 'completed')
            ->where('orders.payment_status', 'paid'); // VÁ LỖI 2.1

        if ($startDate) $query->where('orders.created_at', '>=', $startDate);

        return $query->groupBy('order_items.product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product') 
            ->get();
    }

    // NÂNG CẤP 3.4: Top Danh mục
    private function getTopCategories($startDate)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->where('orders.order_status', 'completed')
            ->where('orders.payment_status', 'paid');

        if ($startDate) $query->where('orders.created_at', '>=', $startDate);

        return $query->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
    }

    private function getVoucherStats($startDate): array
    {
        $query = Order::where('order_status', 'completed')
            ->where('payment_status', 'paid'); // VÁ LỖI 2.2

        if ($startDate) $query->where('created_at', '>=', $startDate);

        $totalDiscount = (clone $query)->sum('discount_amount');
        $topCoupons = (clone $query)->select('coupon_id', DB::raw('COUNT(*) as times_used'))
            ->whereNotNull('coupon_id')
            ->groupBy('coupon_id')
            ->orderByDesc('times_used')
            ->take(3)
            ->with('coupon') 
            ->get();

        return [
            'total_discount_given' => $totalDiscount,
            'top_coupons'          => $topCoupons
        ];
    }
}
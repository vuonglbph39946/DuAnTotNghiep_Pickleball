<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatisticService;
use Illuminate\Http\Request; // Nhớ thêm dòng này

class DashboardController extends Controller
{
    public function index(Request $request, StatisticService $statisticService)
    {
        // Lấy số ngày từ URL (Ví dụ: /admin/dashboard?days=30), mặc định là 7
        $days = $request->input('days', 7); 
        
        $dashboardData = $statisticService->getDashboardData($days);

        // Phải truyền thêm biến $days sang view
        return view('admin.dashboard.index', compact('dashboardData', 'days'));
    }
}
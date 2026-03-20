<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];

        // ĐÃ FIX 1: Không dùng with('address') nữa vì dữ liệu nằm thẳng ở bảng orders
        $query = Order::latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // ĐÃ FIX 2: Tìm thẳng vào cột của orders, không dùng whereHas
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $totalFiltered = $orders->total(); 

        return view('admin.orders.index', compact('orders', 'totalFiltered', 'admin'));
    }

    public function show($id)
    {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];

        // ĐÃ FIX 3: Gỡ bỏ 'address' ra khỏi mảng eager loading
        $order = Order::with(['items.product', 'items.variant', 'statusLogs' => function($q) {
            $q->orderBy('id', 'desc');
        }])->findOrFail($id);
        
        return view('admin.orders.show', compact('order', 'admin'));
    }

    public function updateStatus(Request $request, $id)
    {
        Order::where('id', $id)->update([
            'order_status' => $request->status
        ]);

        return back()->with('success', 'Cập nhật thành công');
    }

    public function print($id)
    {
        $order = Order::with(['items.product', 'items.variant'])->findOrFail($id);
        return view('admin.orders.print', compact('order'));
    }

    
}
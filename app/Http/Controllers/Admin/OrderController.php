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

        // 1. Dùng with('address') để chặn lỗi N+1 Query
        $query = Order::with('address')->latest();

        // 2. Bộ lọc trạng thái
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // 3. Tìm kiếm bằng mã đơn, ID, tên hoặc số điện thoại
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('address', function($subQ) use ($search) {
                      $subQ->where('receiver_name', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // 4. Phân trang 15 đơn/trang
        $orders = $query->paginate(15)->withQueryString();
        $totalFiltered = $orders->total(); // Đếm số đơn để hiện lên Badge

        return view('admin.orders.index', compact('orders', 'totalFiltered', 'admin'));
    }

    public function show($id)
    {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];

        // ĐÃ FIX: Phải gọi items.variant để ra giao diện hiện được màu/size
        $order = Order::with(['items.product', 'items.variant', 'address', 'statusLogs' => function($q) {
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
        // Eager loading đầy đủ dữ liệu để in hóa đơn không bị lỗi
        $order = Order::with(['items.product', 'items.variant', 'address'])->findOrFail($id);
        $address = $order->address; 
        
        return view('admin.orders.print', compact('order', 'address'));
    }
}
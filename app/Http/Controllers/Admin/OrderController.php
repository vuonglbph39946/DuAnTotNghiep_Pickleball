<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();
        return view('admin.orders.index', compact('orders'));
    }


    public function show($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }


    public function updateStatus(Request $request, $id)
    {
        Order::where('id', $id)->update([
            'order_status' => $request->status
        ]);

        return back()->with('success', 'Cập nhật thành công');
    }

    // ==========================================
    // TÍNH NĂNG IN HÓA ĐƠN / VẬN ĐƠN PDF
    // ==========================================
    public function print($id)
    {
        // Gọi dữ liệu đơn hàng và các sản phẩm bên trong
        $order = Order::with(['items.product'])->findOrFail($id);
        
        // Gọi thêm dữ liệu địa chỉ nếu có
        $address = \Illuminate\Support\Facades\DB::table('addresses')->where('id', $order->address_id)->first();
        return view('admin.orders.print', compact('order', 'address'));
    }
}
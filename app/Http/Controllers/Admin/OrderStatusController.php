<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusLog;

class OrderStatusController extends Controller
{
    private $steps = ['pending', 'confirmed', 'shipping', 'completed'];

    // ==========================================
    // 1. HÀM CẬP NHẬT TRẠNG THÁI
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::with('items.product')->findOrFail($id);

            $oldStatus = $order->order_status;
            $newStatus = $request->status;

            if ($oldStatus === $newStatus) {
                return response()->json(['msg' => 'Không có sự thay đổi'], 200);
            }

            if ($oldStatus === 'cancelled' || $oldStatus === 'completed') {
                return response()->json(['error' => 'Đơn hàng đã đóng băng, không thể thao tác!'], 400);
            }

            if ($newStatus !== 'cancelled') {
                $currentIndex = array_search($oldStatus, $this->steps);
                $newIndex = array_search($newStatus, $this->steps);

                if ($currentIndex === false || $newIndex === false) {
                    return response()->json(['error' => 'Trạng thái không hợp lệ!'], 400);
                }

                if ($newIndex !== ($currentIndex + 1)) {
                    return response()->json(['error' => 'Chỉ được chuyển sang trạng thái tiếp theo (1 bước)!'], 400);
                }
            }

            // ===================================================
            // 🥇 LOGIC TỒN KHO 1: RỜI KHỎI PENDING -> TRỪ KHO
            // ===================================================
            $activeStates = ['confirmed', 'shipping', 'completed'];
            $isMovingToActive = ($oldStatus === 'pending') && in_array($newStatus, $activeStates);
            
            if ($isMovingToActive) {
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->stock < $item->quantity) {
                        return response()->json([
                            'error' => 'Sản phẩm "' . $item->product->name . '" không đủ để xử lý đơn!'
                        ], 400);
                    }
                }
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
            }

            // ===================================================
            // 🥇 LOGIC TỒN KHO 2: ĐANG XỬ LÝ MÀ BỊ HUỶ -> CỘNG TRẢ KHO
            // ===================================================
            $isMovingToInactive = in_array($oldStatus, $activeStates) && ($newStatus === 'cancelled');
            
            if ($isMovingToInactive) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            // Cập nhật trạng thái
            $order->update(['order_status' => $newStatus]);
            
            OrderStatusLog::create([
                'order_id' => $id,
                'status' => $newStatus,
                'created_at' => now()
            ]);

            // ĐÃ SỬA: Sắp xếp theo ID giảm dần để chống lỗi chùng thời gian
            $order->load(['items.product', 'statusLogs' => function($q) {
                $q->orderBy('id', 'desc');
            }]);

            $admin = (object)[
                'full_name' => 'Admin Test',
                'avatar' => 'default-avatar.png'
            ];

            return view('admin.orders.show', compact('admin', 'order'));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi Backend: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 2. HÀM HOÀN TÁC (UNDO)
    // ==========================================
    public function undo($id)
    {
        try {
            $order = Order::with('items.product')->findOrFail($id);
            $currentStatus = $order->order_status;

            if ($currentStatus === 'cancelled') {
                return response()->json(['error' => 'Không thể hoàn tác đơn đã huỷ!'], 400);
            }

            $currentIndex = array_search($currentStatus, $this->steps);

            if ($currentIndex === false || $currentIndex === 0) {
                return response()->json(['error' => 'Đang ở bước đầu tiên, không thể lùi!'], 400);
            }

            $previousStatus = $this->steps[$currentIndex - 1];

            // ===================================================
            // 🥇 LOGIC TỒN KHO 3: HOÀN TÁC VỀ PENDING -> CỘNG TRẢ KHO
            // ===================================================
            if ($currentStatus === 'confirmed' && $previousStatus === 'pending') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            $order->update(['order_status' => $previousStatus]);
            
            OrderStatusLog::create([
                'order_id' => $id,
                'status' => $previousStatus,
                'created_at' => now()
            ]);

            // ĐÃ SỬA: Sắp xếp theo ID giảm dần
            $order->load(['items.product', 'statusLogs' => function($q) {
                $q->orderBy('id', 'desc');
            }]);

            $admin = (object)[
                'full_name' => 'Admin Test',
                'avatar' => 'default-avatar.png'
            ];

            return view('admin.orders.show', compact('admin', 'order'));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi Backend: ' . $e->getMessage()], 500);
        }
    }
}
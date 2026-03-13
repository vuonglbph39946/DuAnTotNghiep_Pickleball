<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class OrderStatusController extends Controller
{
    private $steps = ['pending', 'confirmed', 'shipping', 'completed'];

    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // LỖ HỔNG 1 ĐÃ VÁ: Dùng lockForUpdate() để khóa dòng, chống Race Condition (Tranh giành thao tác)
            $order = Order::with(['items.product', 'items.variant'])->where('id', $id)->lockForUpdate()->firstOrFail();

            $oldStatus = $order->order_status;
            $newStatus = $request->status;

            if ($oldStatus === $newStatus) {
                DB::rollBack();
                return response()->json(['msg' => 'Không có sự thay đổi'], 200);
            }

            if (in_array($oldStatus, ['cancelled', 'completed'])) {
                DB::rollBack();
                return response()->json(['error' => 'Đơn hàng đã đóng băng, không thể thao tác!'], 400);
            }

            if ($newStatus !== 'cancelled') {
                $currentIndex = array_search($oldStatus, $this->steps);
                $newIndex = array_search($newStatus, $this->steps);

                if ($currentIndex === false || $newIndex === false || $newIndex !== ($currentIndex + 1)) {
                    DB::rollBack();
                    return response()->json(['error' => 'Trạng thái không hợp lệ!'], 400);
                }
            }

            // ===================================================
            // 🚨 ĐÃ VÁ LỖ HỔNG 2: CHẶN DUYỆT ĐƠN ONLINE CHƯA THANH TOÁN
            // ===================================================
            if (in_array(strtolower($order->payment_method), ['momo', 'vnpay']) 
                && $order->payment_status === 'unpaid' 
                && $newStatus !== 'cancelled') {
                DB::rollBack();
                return response()->json(['error' => 'Đơn hàng thanh toán Online chưa được thanh toán. Không thể duyệt!'], 400);
            }

            // ===================================================
            // 🥇 LOGIC TỒN KHO 1: RỜI KHỎI PENDING -> TRỪ KHO CHUẨN XÁC
            // ===================================================
            $activeStates = ['confirmed', 'shipping', 'completed'];
            $isMovingToActive = ($oldStatus === 'pending') && in_array($newStatus, $activeStates);
            
            if ($isMovingToActive) {
                // 1. Kiểm tra tồn kho toàn bộ đơn hàng trước (Lock dữ liệu Kho để so sánh)
                foreach ($order->items as $item) {
                    if ($item->product_variant_id && $item->variant) {
                        $variant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();
                        if (!$variant || $variant->stock < $item->quantity) {
                            DB::rollBack();
                            $variantName = $item->variant_info ?? 'Biến thể này';
                            return response()->json(['error' => 'Sản phẩm "'.$item->product->name.' ('.$variantName.')" không đủ hàng!'], 400);
                        }
                    } else if ($item->product) {
                        $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                        if (!$product || $product->stock < $item->quantity) {
                            DB::rollBack();
                            return response()->json(['error' => 'Sản phẩm "'.$item->product->name.'" không đủ hàng!'], 400);
                        }
                    }
                }
                
                // 2. ĐÃ FIX: Trừ tồn kho đồng bộ (Cả biến thể lẫn Sản phẩm gốc)
                foreach ($order->items as $item) {
                    // Luôn luôn trừ Tổng tồn kho của Sản phẩm gốc
                    if ($item->product) {
                        Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
                    }
                    // Bỏ "else if", dùng "if" độc lập để trừ tiếp cho biến thể (nếu có)
                    if ($item->product_variant_id && $item->variant) {
                        ProductVariant::where('id', $item->product_variant_id)->decrement('stock', $item->quantity);
                    }
                }
            }

            $updateData = ['order_status' => $newStatus];

            // ===================================================
            // 🥇 LOGIC TỒN KHO 2: ĐANG XỬ LÝ MÀ BỊ HUỶ -> CỘNG TRẢ LẠI
            // ===================================================
            $isMovingToInactive = in_array($oldStatus, $activeStates) && ($newStatus === 'cancelled');
            if ($isMovingToInactive) {
                foreach ($order->items as $item) {
                    // ĐÃ FIX: Hoàn lại cả Tổng tồn kho và Tồn kho biến thể độc lập
                    if ($item->product) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                    if ($item->product_variant_id && $item->variant) {
                        ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                    }
                }

                // LỖ HỔNG 3 ĐÃ VÁ: TỰ ĐỘNG HOÀN TIỀN CHO ĐƠN HÀNG ONLINE
                if ($order->payment_status === 'paid' && in_array(strtolower($order->payment_method), ['momo', 'vnpay'])) {
                    $updateData['payment_status'] = 'refunded';
                }
            }

            // ===================================================
            // 🥇 TỰ ĐỘNG CẬP NHẬT ĐÃ THANH TOÁN KHI HOÀN THÀNH (ĐỐI VỚI COD)
            // ===================================================
            if ($newStatus === 'completed') {
                $updateData['payment_status'] = 'paid';
            }

            // Lưu dữ liệu Order
            $order->update($updateData);
            
            // Ghi Log
            OrderStatusLog::create([
                'order_id' => $id,
                'status' => $newStatus,
                'created_at' => now()
            ]);

            DB::commit(); // Xong việc -> Mở khóa dữ liệu

            // Trả về view cập nhật
            $order->load(['items.product', 'items.variant', 'statusLogs' => function($q) {
                $q->orderBy('id', 'desc');
            }]);
            $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
            return view('admin.orders.show', compact('admin', 'order'));

        } catch (\Exception $e) {
            DB::rollBack(); // Lỗi bất ngờ -> Nhả khóa, hủy bỏ mọi thao tác
            return response()->json(['error' => 'Lỗi Backend: ' . $e->getMessage()], 500);
        }
    }

    public function undo($id)
    {
        try {
            DB::beginTransaction();
            $order = Order::with(['items.product', 'items.variant'])->where('id', $id)->lockForUpdate()->firstOrFail();
            
            $currentStatus = $order->order_status;

            if (in_array($currentStatus, ['cancelled', 'completed'])) {
                DB::rollBack();
                return response()->json(['error' => 'Đơn hàng đã đóng băng (Hoàn thành / Đã hủy), không thể hoàn tác!'], 400);
            }

            $currentIndex = array_search($currentStatus, $this->steps);
            if ($currentIndex === false || $currentIndex === 0) {
                DB::rollBack();
                return response()->json(['error' => 'Đang ở bước đầu tiên, không thể lùi!'], 400);
            }

            $previousStatus = $this->steps[$currentIndex - 1];

            // 🥇 LOGIC TỒN KHO 3: HOÀN TÁC VỀ PENDING -> CỘNG TRẢ KHO
            if ($currentStatus === 'confirmed' && $previousStatus === 'pending') {
                foreach ($order->items as $item) {
                    // ĐÃ FIX: Hoàn lại cả Tổng tồn kho và Tồn kho biến thể
                    if ($item->product) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                    // Bỏ "else if"
                    if ($item->product_variant_id && $item->variant) {
                        ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $order->update(['order_status' => $previousStatus]);
            
            OrderStatusLog::create([
                'order_id' => $id,
                'status' => $previousStatus,
                'created_at' => now()
            ]);

            DB::commit();

            $order->load(['items.product', 'items.variant', 'statusLogs' => function($q) {
                $q->orderBy('id', 'desc');
            }]);
            $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
            return view('admin.orders.show', compact('admin', 'order'));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi Backend: ' . $e->getMessage()], 500);
        }
    }
}
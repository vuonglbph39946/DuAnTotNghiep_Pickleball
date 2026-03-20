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

    // =========================================================
    // 1. HÀM CẬP NHẬT TRẠNG THÁI (ĐƠN HÀNG & THANH TOÁN)
    // =========================================================
   // =========================================================
    // 1. HÀM CẬP NHẬT TRẠNG THÁI (ĐƠN HÀNG & THANH TOÁN)
    // =========================================================
    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $order = Order::with(['items.product', 'items.variant'])->where('id', $id)->lockForUpdate()->firstOrFail();

            $oldStatus = $order->order_status;
            $newStatus = $request->status ?? $oldStatus;
            
            $oldPaymentStatus = $order->payment_status;
            $newPaymentStatus = $request->payment_status ?? $oldPaymentStatus;

            if ($oldStatus === $newStatus && $oldPaymentStatus === $newPaymentStatus) {
                DB::rollBack();
                return $request->wantsJson() ? response()->json(['msg' => 'Không có sự thay đổi'], 200) : back()->with('info', 'Không có sự thay đổi');
            }

            // --------------------------------------------------
            // A. XỬ LÝ LOGIC TRẠNG THÁI ĐƠN HÀNG
            // --------------------------------------------------
            if ($oldStatus !== $newStatus) {
                if (in_array($oldStatus, ['cancelled', 'completed'])) {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Đơn hàng đã đóng băng, không thể thay đổi trạng thái!'], 400) : back()->with('error', 'Đơn hàng đã đóng băng, không thể thay đổi trạng thái!');
                }

                if ($oldStatus === 'shipping' && $newStatus === 'cancelled') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Đơn hàng đã được giao cho Shipper, không thể hủy!'], 400) : back()->with('error', 'Đơn hàng đã được giao cho Shipper, không thể hủy!');
                }

                if ($newStatus !== 'cancelled') {
                    $currentIndex = array_search($oldStatus, $this->steps);
                    $newIndex = array_search($newStatus, $this->steps);

                    if ($newIndex !== false && $currentIndex !== false) {
                        if ($newIndex < $currentIndex) {
                            DB::rollBack();
                            return $request->wantsJson() ? response()->json(['error' => 'Không thể lùi trạng thái đơn hàng.'], 400) : back()->with('error', 'Không thể lùi trạng thái đơn hàng.');
                        }
                        if ($newIndex > $currentIndex + 1) {
                            DB::rollBack();
                            return $request->wantsJson() ? response()->json(['error' => 'Chỉ được chuyển sang trạng thái tiếp theo, không được nhảy cóc!'], 400) : back()->with('error', 'Không được nhảy cóc trạng thái!');
                        }
                    }
                }

                if ($newStatus === 'cancelled' && $oldStatus === 'confirmed') {
                    foreach ($order->items as $item) {
                        if ($item->product) Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                        if ($item->product_variant_id && $item->variant) ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                    }
                }

                if ($newStatus === 'confirmed' && $oldStatus === 'pending') {
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            if ($item->product->stock < $item->quantity) {
                                DB::rollBack();
                                return $request->wantsJson() ? response()->json(['error' => "Sản phẩm '{$item->product->name}' không đủ tồn kho!"], 400) : back()->with('error', "Sản phẩm '{$item->product->name}' không đủ tồn kho!");
                            }
                            Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
                        }
                        if ($item->product_variant_id && $item->variant) {
                            if ($item->variant->stock < $item->quantity) {
                                DB::rollBack();
                                return $request->wantsJson() ? response()->json(['error' => "Phân loại '{$item->variant_info}' không đủ tồn kho!"], 400) : back()->with('error', "Phân loại không đủ tồn kho!");
                            }
                            ProductVariant::where('id', $item->product_variant_id)->decrement('stock', $item->quantity);
                        }
                    }
                }

                OrderStatusLog::create(['order_id' => $id, 'status' => $newStatus, 'created_at' => now()]);
            }

            // --------------------------------------------------
            // B. XỬ LÝ LOGIC TRẠNG THÁI THANH TOÁN (LUẬT MỚI)
            // --------------------------------------------------
            if ($oldPaymentStatus !== $newPaymentStatus) {
                // 1. Không cho đi lùi (Đã TT -> Chưa TT)
                if ($oldPaymentStatus === 'paid' && $newPaymentStatus === 'unpaid') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Không thể lùi từ Đã thanh toán về Chưa thanh toán!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }
                
                // 2. Đã hoàn tiền thì cấm đụng vào nữa
                if ($oldPaymentStatus === 'refunded') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Đơn hàng đã hoàn tiền, không thể thay đổi nữa!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }

                // 3. Đơn COD cấm chọn Hoàn tiền
                if ($order->payment_method === 'cod' && $newPaymentStatus === 'refunded') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Đơn COD không có chức năng Hoàn tiền!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }

                // 4. Hủy COD cấm cập nhật thanh toán
                if ($order->payment_method === 'cod' && $oldStatus === 'cancelled') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Đơn COD đã hủy không thể cập nhật thanh toán!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }
            }

            $order->update([
                'order_status' => $newStatus,
                'payment_status' => $newPaymentStatus 
            ]);

            DB::commit();
            return $request->wantsJson() ? response()->json(['msg' => 'Cập nhật thành công']) : back()->with('success', 'Cập nhật thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return $request->wantsJson() ? response()->json(['error' => 'Lỗi: ' . $e->getMessage()], 500) : back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    
}
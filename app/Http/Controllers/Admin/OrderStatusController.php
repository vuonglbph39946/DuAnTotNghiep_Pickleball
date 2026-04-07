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
                        if ($newIndex <= $currentIndex) {
                            DB::rollBack();
                            return $request->wantsJson() ? response()->json(['error' => 'Không thể lùi hoặc lưu lại trạng thái cũ.'], 400) : back()->with('error', 'Không thể lùi trạng thái đơn hàng.');
                        }
                        if ($newIndex > $currentIndex + 1) {
                            DB::rollBack();
                            return $request->wantsJson() ? response()->json(['error' => 'Chỉ được chuyển sang trạng thái tiếp theo, không được nhảy cóc!'], 400) : back()->with('error', 'Không được nhảy cóc trạng thái!');
                        }
                    }
                }

                // === ĐÃ FIX LỖI 2: CHỈ HOÀN LẠI KHI TRẠNG THÁI CŨ KHÁC 'cancelled' ===
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    
                    // 1. Hoàn Tồn kho Sản phẩm (Nếu trước đó đã bị trừ ở bước 'confirmed')
                    if ($oldStatus === 'confirmed') {
                        foreach ($order->items as $item) {
                            if ($item->product) Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                            if ($item->product_variant_id && $item->variant) ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                        }
                    }

                    // 2. HOÀN LẠI VOUCHER VÀ LƯỢT DÙNG CÁ NHÂN
                    if ($order->coupon_id) {
                        \App\Models\Coupon::where('id', $order->coupon_id)->increment('quantity', 1);
                        if ($order->user_id) {
                            \DB::table('coupon_user')
                                ->where('coupon_id', $order->coupon_id)
                                ->where('user_id', $order->user_id)
                                ->limit(1) // ĐÃ FIX LỖI 1: CHỈ XÓA ĐÚNG 1 LẦN DÙNG CỦA ĐƠN NÀY
                                ->delete();
                        }
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
            // B. XỬ LÝ LOGIC TRẠNG THÁI THANH TOÁN
            // --------------------------------------------------
            if ($oldPaymentStatus !== $newPaymentStatus) {
                if ($oldPaymentStatus === 'paid' && $newPaymentStatus === 'unpaid') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Không thể lùi từ Đã thanh toán về Chưa thanh toán!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }
                
                if ($oldPaymentStatus === 'refunded') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Đơn hàng đã hoàn tiền, không thể thay đổi nữa!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }

                if ($order->payment_method === 'cod' && $newPaymentStatus === 'refunded') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Đơn COD không có chức năng Hoàn tiền!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }

                if (in_array($oldStatus, ['cancelled', 'returned'])) {
                    $isValidRefund = ($oldPaymentStatus === 'paid' && $newPaymentStatus === 'refunded' && $order->payment_method !== 'cod');
                    if (!$isValidRefund) {
                        DB::rollBack();
                        return $request->wantsJson() ? response()->json(['error' => 'Đơn hàng đã hủy, chỉ được phép cập nhật Hoàn tiền cho đơn Online đã thanh toán!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                    }
                }

                // Không cho hoàn tiền nếu Đơn hàng đang giao hoặc đã giao
                if ($newPaymentStatus === 'refunded' && in_array($oldStatus, ['shipping', 'completed'])) {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Không thể Hoàn tiền khi đơn hàng đang được giao hoặc đã hoàn thành!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }

                if (in_array($order->payment_method, ['vnpay', 'momo']) && $oldPaymentStatus === 'unpaid' && $newPaymentStatus === 'paid') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Giao dịch Online phải do hệ thống tự động xác nhận, Admin không được tự đổi!'], 400) : back()->with('error', 'Lỗi thao tác thanh toán!');
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
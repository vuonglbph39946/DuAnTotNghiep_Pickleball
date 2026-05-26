<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Payment;
use App\Models\Coupon;

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

                // === CHỈ HOÀN LẠI KHI TRẠNG THÁI CŨ KHÁC 'cancelled' ===
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    
                    // 1. Hoàn Tồn kho Sản phẩm
                    if ($oldStatus === 'confirmed') {
                        foreach ($order->items as $item) {
                            if ($item->product) Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                            if ($item->product_variant_id && $item->variant) ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                        }
                    }

                    // 2. HOÀN LẠI VOUCHER
                    if ($order->coupon_id) {
                        \App\Models\Coupon::where('id', $order->coupon_id)->increment('quantity', 1);
                        if ($order->user_id) {
                            \DB::table('coupon_user')
                                ->where('coupon_id', $order->coupon_id)
                                ->where('user_id', $order->user_id)
                                ->limit(1)
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

                if ($newPaymentStatus === 'refunded' && in_array($oldStatus, ['shipping', 'completed'])) {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Không thể Hoàn tiền khi đơn hàng đang được giao hoặc đã hoàn thành!'], 400) : back()->with('error', 'Lỗi trạng thái thanh toán!');
                }

                if ($oldPaymentStatus === 'paid' && $newPaymentStatus === 'refunded' && in_array($order->payment_method, ['vnpay', 'momo'])) {
                    DB::rollBack();
                    return $request->wantsJson() 
                        ? response()->json(['error' => 'Giao dịch Online không được hoàn tiền thủ công! Vui lòng dùng nút kích hoạt API ở Banner màu đỏ.'], 400) 
                        : back()->with('error', 'Không thể hoàn tiền thủ công cho phương thức thanh toán Online!');
                }

                if (in_array($order->payment_method, ['vnpay', 'momo']) && $oldPaymentStatus === 'unpaid' && $newPaymentStatus === 'paid') {
                    DB::rollBack();
                    return $request->wantsJson() ? response()->json(['error' => 'Giao dịch Online phải do hệ thống tự động xác nhận, Admin không được tự đổi!'], 400) : back()->with('error', 'Lỗi thao tác thanh toán!');
                }

                // === ĐÃ FIX: CHẶN ADMIN ĐỔI THỦ CÔNG SANG "ĐÃ THANH TOÁN" CHO ĐƠN COD (NẾU CHƯA HOÀN THÀNH) ===
                if ($order->payment_method === 'cod' && $oldPaymentStatus === 'unpaid' && $newPaymentStatus === 'paid') {
                    if ($newStatus !== 'completed') {
                        DB::rollBack();
                        return $request->wantsJson() 
                            ? response()->json(['error' => 'Đơn COD chỉ được ghi nhận "Đã thanh toán" khi trạng thái giao hàng là "Hoàn thành"!'], 400) 
                            : back()->with('error', 'Đơn COD chưa giao xong không thể thu tiền!');
                    }
                }
            }

            // =========================================================
            // C. TỰ ĐỘNG HÓA: KHI ĐƠN COD CHUYỂN SANG HOÀN THÀNH -> TỰ ĐỘNG ĐÃ THANH TOÁN
            // =========================================================
            if ($order->payment_method === 'cod' && $newStatus === 'completed' && $oldPaymentStatus === 'unpaid') {
                $newPaymentStatus = 'paid';
                
                \App\Models\Payment::create([
                    'order_id' => $order->id,
                    'payment_gateway' => 'cod',
                    'transaction_code' => 'COD_ADMIN_' . time(),
                    'payment_status' => 'success',
                    'amount' => $order->total_amount,
                    'status' => 'Admin xác nhận giao thành công và thu tiền COD'
                ]);
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

    // ==========================================
    // 1. ADMIN ĐỒNG Ý HỦY ĐƠN HÀNG (TRẢ LẠI STOCK, VOUCHER)
    // ==========================================
    public function approveCancel($id)
    {
        try {
            DB::beginTransaction();
            // Lấy order kèm items để xử lý stock
            $order = Order::with('items')->lockForUpdate()->findOrFail($id);

            if ($order->order_status !== 'cancel_requested') {
                throw new \Exception('Đơn hàng không ở trạng thái Yêu cầu hủy!');
            }

            // Đổi trạng thái thành Đã hủy
            $order->update(['order_status' => 'cancelled']);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'created_at' => now()
            ]);

            // HOÀN LẠI TỒN KHO (STOCK) CHO SẢN PHẨM
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    \App\Models\ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                } else {
                    \App\Models\Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            // HOÀN LẠI VOUCHER CHO KHÁCH
            if ($order->coupon_id) {
                Coupon::where('id', $order->coupon_id)->increment('quantity', 1);
                if ($order->user_id) {
                    DB::table('coupon_user')
                        ->where('coupon_id', $order->coupon_id)
                        ->where('user_id', $order->user_id)
                        ->limit(1)
                        ->delete();
                }
            }

            DB::commit();
            return back()->with('success', 'Đã duyệt đồng ý hủy đơn! Đã hoàn lại Tồn kho và Mã giảm giá thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // ==========================================
    // 2. ADMIN TỪ CHỐI HỦY ĐƠN HÀNG (TIẾP TỤC GIAO HÀNG)
    // ==========================================
    public function rejectCancel(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $order = Order::lockForUpdate()->findOrFail($id);

            if ($order->order_status !== 'cancel_requested') {
                throw new \Exception('Đơn hàng không ở trạng thái Yêu cầu hủy!');
            }

            // Trả đơn hàng về trạng thái Đã xác nhận để tiếp tục quy trình đóng gói/giao hàng
            $order->update(['order_status' => 'confirmed']);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'confirmed',
                'created_at' => now()
            ]);

            DB::commit();
            return back()->with('success', 'Đã từ chối hủy. Đơn hàng sẽ tiếp tục được xử lý và giao đi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // ==========================================
    // 3. API HOÀN TIỀN VNPAY
    // ==========================================
    public function refundVNPay(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->order_status !== 'cancelled') {
            return back()->with('error', 'Chỉ có thể hoàn tiền cho đơn hàng ĐÃ HỦY!');
        }

        $payment = Payment::where('order_id', $order->id)
            ->where('payment_status', 'success')
            ->where('payment_gateway', 'vnpay')
            ->first();

        if (!$payment) {
            return back()->with('error', 'Không tìm thấy giao dịch thanh toán VNPay gốc!');
        }

        // Thông số cấu hình VNPAY (Lấy từ .env)
        $vnp_TmnCode = env('VNP_TMN_CODE', '334KPU27');
        $vnp_HashSecret = env('VNP_HASH_SECRET', 'Q4EXRFYNPBWN6T1LSKLHIP3VCCTMGWMA');
        $vnp_Url = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";

        $vnp_RequestId = uniqid();
        $vnp_Version = '2.1.0';
        $vnp_Command = 'refund';
        $vnp_TransactionType = '02'; // Hoàn toàn phần
        $vnp_TxnRef = $order->order_code;
        $vnp_Amount = $order->total_amount * 100;
        $vnp_TransactionNo = $payment->transaction_code ?? "0";
        $vnp_TransactionDate = date('YmdHis', strtotime($payment->created_at));
        $vnp_CreateBy = \Illuminate\Support\Facades\Auth::user()->name ?? 'Admin';
        $vnp_CreateDate = date('YmdHis');
        $vnp_IpAddr = $request->ip();
        $vnp_OrderInfo = "Hoan tien don hang " . $order->order_code;

        $datastr = $vnp_RequestId . "|" . $vnp_Version . "|" . $vnp_Command . "|" . $vnp_TmnCode . "|" . $vnp_TransactionType . "|" . $vnp_TxnRef . "|" . $vnp_Amount . "|" . $vnp_TransactionNo . "|" . $vnp_TransactionDate . "|" . $vnp_CreateBy . "|" . $vnp_CreateDate . "|" . $vnp_IpAddr . "|" . $vnp_OrderInfo;
        $vnp_SecureHash = hash_hmac('sha512', $datastr, $vnp_HashSecret);

        $data = [
            "vnp_RequestId" => $vnp_RequestId,
            "vnp_Version" => $vnp_Version,
            "vnp_Command" => $vnp_Command,
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_TransactionType" => $vnp_TransactionType,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_Amount" => $vnp_Amount,
            "vnp_TransactionNo" => $vnp_TransactionNo,
            "vnp_TransactionDate" => $vnp_TransactionDate,
            "vnp_CreateBy" => $vnp_CreateBy,
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_SecureHash" => $vnp_SecureHash
        ];

        $response = Http::post($vnp_Url, $data);
        $result = $response->json();

        if ($result) {
            if (($result['vnp_ResponseCode'] ?? '') == '00') {
                $payment->update(['vnp_refund_transaction_no' => $result['vnp_TransactionNo']]);
                $order->update(['payment_status' => 'refunded']);
                return back()->with('success', 'Hoàn tiền tự động VNPay thành công! Mã biên lai: ' . $result['vnp_TransactionNo']);
            } else {
                return back()->with('error', 'VNPay từ chối xử lý: ' . ($result['vnp_Message'] ?? 'Lỗi không xác định'));
            }
        }
        return back()->with('error', 'Không thể kết nối đến máy chủ VNPay!');
    }
}
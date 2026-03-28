<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\UserAddress;
use App\Models\Payment; // ĐÃ THÊM MODEL PAYMENT
use App\Mail\OrderSuccessMail;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += $item['price'] * $item['quantity'];
        }

        $shippingFee = $subTotal > 500000 ? 0 : 30000; 
        $totalAmount = $subTotal + $shippingFee;

        $addresses = collect(); 
        
        if (Auth::check()) {
            $user = Auth::user();
            $addresses = UserAddress::where('user_id', $user->id)->get();
        }

        return view('client.checkout.index', compact('cart', 'subTotal', 'shippingFee', 'totalAmount', 'addresses'));
    }

    public function process(Request $request)
    {
        // Chỉ cho phép COD hoặc VNPAY
        $request->validate([
            'payment_method' => 'required|in:cod,vnpay',
        ]);

        DB::beginTransaction();
        try {
            $customer_name = "";
            $customer_phone = "";
            $customer_email = "";
            $province_id = "";
            $province_name = "";
            $district_id = "";
            $district_name = "";
            $ward_id = "";
            $ward_name = "";
            $specific_address = "";

            if ($request->address_id) {
                $address = UserAddress::find($request->address_id);
                if ($address) {
                    $customer_name = $address->customer_name;
                    $customer_phone = $address->customer_phone;
                    $customer_email = Auth::user()->email ?? 'guest@example.com';
                    $province_id = $address->province_id;
                    $province_name = $address->province_name;
                    $district_id = $address->district_id;
                    $district_name = $address->district_name;
                    $ward_id = $address->ward_id;
                    $ward_name = $address->ward_name;
                    $specific_address = $address->specific_address;
                }
            } else {
                $customer_name = $request->customer_name;
                $customer_phone = $request->customer_phone;
                $customer_email = $request->customer_email;
                $province_id = $request->province_id;
                $province_name = $request->province_name;
                $district_id = $request->district_id;
                $district_name = $request->district_name;
                $ward_id = $request->ward_id;
                $ward_name = $request->ward_name;
                $specific_address = $request->specific_address;
            }

            $cart = session()->get('cart', []);
            if (empty($cart)) {
                throw new \Exception('Giỏ hàng trống!');
            }

            $subTotal = 0;
            $orderItemsData = [];
            foreach ($cart as $id => $item) {
                $subTotal += $item['price'] * $item['quantity'];
                
                if (isset($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    if (!$variant || $variant->stock < $item['quantity']) {
                        throw new \Exception('Sản phẩm ' . $item['name'] . ' không đủ số lượng.');
                    }
                } else {
                    $product = Product::find($item['product_id']);
                    if (!$product || $product->stock < $item['quantity']) {
                        throw new \Exception('Sản phẩm ' . $item['name'] . ' không đủ số lượng.');
                    }
                }

                $orderItemsData[] = [
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'variant_info' => $item['variant_info'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
            }

            $shippingFee = $subTotal > 500000 ? 0 : 30000; 
            $totalAmount = $subTotal + $shippingFee;

            $orderCode = 'PB' . strtoupper(substr(md5(uniqid()), 0, 13));

            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => Auth::id() ?? null,
                'total_amount' => $totalAmount,
                'shipping_fee' => $shippingFee,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'customer_name' => $customer_name,
                'customer_phone' => $customer_phone,
                'customer_email' => $customer_email,
                'province_id' => $province_id,
                'province_name' => $province_name,
                'district_id' => $district_id,
                'district_name' => $district_name,
                'ward_id' => $ward_id,
                'ward_name' => $ward_name,
                'specific_address' => $specific_address,
                'note' => $request->order_note
            ]);

            foreach ($orderItemsData as &$iData) {
                $iData['order_id'] = $order->id;
            }
            OrderItem::insert($orderItemsData);

            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'created_at' => now()
            ]);

            // KHÔNG XÓA GIỎ HÀNG Ở ĐÂY NỮA
            DB::commit(); 

            // ==========================================
            // NẾU LÀ VNPAY: CHUYỂN HƯỚNG SANG CỔNG THANH TOÁN
            // ==========================================
            if ($request->payment_method == 'vnpay') {
                $vnp_Url = env('VNP_URL', "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html");
                $vnp_Returnurl = env('VNP_RETURN_URL', url('/checkout/vnpay-return'));
                $vnp_TmnCode = env('VNP_TMN_CODE', "8FH097LV");
                $vnp_HashSecret = env('VNP_HASH_SECRET', "BJ08XFLOMBM5L0FRX8XTA9WY5SW97FR9");

                $vnp_TxnRef = $order->order_code;
                $vnp_OrderInfo = $order->order_code; // Nội dung ghi chú bắt buộc là Mã Đơn Hàng
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = $order->total_amount * 100; // VNPAY yêu cầu nhân 100
                $vnp_Locale = 'vn';
                $vnp_BankCode = ''; 
                $vnp_IpAddr = $request->ip();

                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef
                );

                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }

                return redirect()->away($vnp_Url);
            }

            // ==========================================
            // NẾU LÀ COD: GỬI MAIL, XÓA GIỎ HÀNG VÀ SANG SUCCESS
            // ==========================================
            try {
                Mail::to($order->customer_email)->send(new OrderSuccessMail($order));
            } catch (\Throwable $e) {
                \Log::error('Lỗi gửi mail: ' . $e->getMessage());
            }

            session()->forget('cart'); 
            return redirect()->route('checkout.success', $order->order_code);

        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // NHẬN KẾT QUẢ TỪ VNPAY VÀ CẬP NHẬT ĐƠN HÀNG
    // ==========================================
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET', "BJ08XFLOMBM5L0FRX8XTA9WY5SW97FR9");
        $inputData = array();
        
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $order_code = $inputData['vnp_TxnRef'] ?? null;
        $vnp_TransactionNo = $inputData['vnp_TransactionNo'] ?? 'Unknown'; 
        
        $order = Order::where('order_code', $order_code)->first();
        
        if ($secureHash === $vnp_SecureHash) {
            // NẾU THANH TOÁN THÀNH CÔNG (Mã 00)
            if ($inputData['vnp_ResponseCode'] == '00') {
                if ($order && $order->payment_status == 'unpaid') {
                    // 1. Cập nhật Đơn hàng
                    $order->update(['payment_status' => 'paid']);
                    
                    // 2. Ghi Log vào bảng Payments
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_gateway' => 'vnpay',
                        'transaction_code' => $vnp_TransactionNo,
                        'payment_status' => 'success',
                        'amount' => $order->total_amount,
                        'status' => 'Thanh toán VNPay thành công',
                    ]);
                    
                    // 3. Gửi Mail
                    try {
                        Mail::to($order->customer_email)->send(new OrderSuccessMail($order));
                    } catch (\Throwable $e) {
                        \Log::error('Lỗi gửi mail VNPay: ' . $e->getMessage());
                    }
                }
                
                session()->forget('cart'); // Xóa giỏ hàng
                return redirect()->route('checkout.success', $order_code)->with('success', 'Thanh toán qua VNPay thành công!');
                
            } else {
                // NẾU KHÁCH BẤM HỦY HOẶC LỖI THẺ -> TỰ ĐỘNG HỦY ĐƠN HÀNG
                if ($order && $order->order_status != 'cancelled') {
                    $order->update(['order_status' => 'cancelled']);
                    
                    OrderStatusLog::create([
                        'order_id' => $order->id,
                        'status' => 'cancelled',
                        'created_at' => now()
                    ]);

                    // Ghi Log vào bảng Payments (Failed)
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_gateway' => 'vnpay',
                        'transaction_code' => $vnp_TransactionNo,
                        'payment_status' => 'failed',
                        'amount' => $order->total_amount,
                        'status' => 'Giao dịch thất bại/Hủy (Mã lỗi: ' . $inputData['vnp_ResponseCode'] . ')',
                    ]);
                }

                return redirect()->route('checkout.index')->with('error', 'Giao dịch thanh toán VNPAY thất bại hoặc bị hủy. Đơn hàng tạm thời đã bị hủy!');
            }
        } else {
            // NẾU SAI CHỮ KÝ BẢO MẬT
            if ($order && $order->order_status != 'cancelled') {
                $order->update(['order_status' => 'cancelled']);
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => 'cancelled',
                    'created_at' => now()
                ]);
                
                Payment::create([
                    'order_id' => $order->id,
                    'payment_gateway' => 'vnpay',
                    'transaction_code' => $vnp_TransactionNo,
                    'payment_status' => 'failed',
                    'amount' => $order->total_amount,
                    'status' => 'Sai chữ ký bảo mật VNPay',
                ]);
            }
            return redirect()->route('checkout.index')->with('error', 'Chữ ký VNPay không hợp lệ. Đơn hàng đã bị hủy!');
        }
    }

    public function success($order_code)
    {
        $order = Order::with('items.product')->where('order_code', $order_code)->firstOrFail();
        return view('client.checkout.success', compact('order'));
    }
}
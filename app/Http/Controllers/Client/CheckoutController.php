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
use App\Models\Payment;
use App\Mail\OrderSuccessMail;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    // === BƠM SHIPPING SERVICE VÀO THAM SỐ ===
    public function index(\App\Services\CouponService $couponService, \App\Services\ShippingService $shippingService)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        $subTotal = 0;
        foreach ($cart as $item) {
            $subTotal += $item['price'] * $item['quantity'];
        }

        // === GỌI TỪ SERVICE ===
        $shippingFee = $shippingService->calculateFee($subTotal);
        
        $discountAmount = 0;
        $couponCode = null;
        
        if (session()->has('applied_coupon')) {
            $appliedCoupon = session('applied_coupon');
            
            // Kiểm tra thời gian áp mã (Quá 30 phút bắt buộc áp lại)
            if (isset($appliedCoupon['applied_at']) && now()->diffInMinutes($appliedCoupon['applied_at']) > 30) {
                session()->forget('applied_coupon');
                return redirect()->route('checkout.index')->with('error', 'Thời gian áp dụng mã giảm giá đã quá hạn (30 phút). Vui lòng nhập lại mã.');
            }

            $couponCode = $appliedCoupon['code'];
            
            $couponResult = $couponService->validateAndCalculate($couponCode, $subTotal, Auth::id(), false);
            
            if ($couponResult['success']) {
                $discountAmount = $couponResult['discount_amount'];
            } else {
                session()->forget('applied_coupon'); // Xóa nếu không còn hợp lệ
                $couponCode = null;
            }
        }

        $totalAmount = $subTotal + $shippingFee - $discountAmount;
        if ($totalAmount < 0) $totalAmount = 0; // Chống âm tiền

        // === ĐÃ DỌN DẸP: Bỏ if(Auth::check()) vì Route đã bắt buộc đăng nhập ===
        $user = Auth::user();
        $addresses = UserAddress::where('user_id', $user->id)->get();

        $availableCoupons = \App\Models\Coupon::where('status', 1)
            ->where('quantity', '>', 0)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();

        // === ĐÃ DỌN DẸP: Bỏ if(Auth::check()) ===
        $userId = $user->id;
        $availableCoupons = $availableCoupons->filter(function($coupon) use ($userId) {
            $usageCount = \DB::table('coupon_user')
                ->where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();
            return $usageCount < $coupon->max_usage_per_user;
        });

        return view('client.checkout.index', compact('cart', 'subTotal', 'shippingFee', 'discountAmount', 'couponCode', 'totalAmount', 'addresses', 'availableCoupons'));
    }

    // === BƠM SHIPPING SERVICE VÀO THAM SỐ ===
    public function process(Request $request, \App\Services\CouponService $couponService, \App\Services\ShippingService $shippingService)
    {
        $rules = [
            'payment_method' => 'required|in:cod,vnpay',
        ];

        if (!$request->address_id) {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = ['required', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'];
            $rules['province_id'] = 'required';
            $rules['district_id'] = 'required';
            $rules['ward_id'] = 'required';
            $rules['specific_address'] = 'required|string|max:255';
        }

        $request->validate($rules, [
            'customer_name.required' => 'Vui lòng nhập họ và tên.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'customer_phone.regex' => 'Số điện thoại không hợp lệ.',
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'ward_id.required' => 'Vui lòng chọn Phường/Xã.',
            'specific_address.required' => 'Vui lòng nhập địa chỉ chi tiết.',
        ]);

        DB::beginTransaction();
        try {
            $customer_name = "";
            $customer_phone = "";
            $province_id = "";
            $province_name = "";
            $district_id = "";
            $district_name = "";
            $ward_id = "";
            $ward_name = "";
            $specific_address = "";

            // === ĐÃ DỌN DẸP: Luôn lấy email của user đang đăng nhập ===
            $user = Auth::user();
            $customer_email = $user->email;

            if ($request->address_id) {
                $address = UserAddress::find($request->address_id);
                if ($address) {
                    $customer_name = $address->customer_name;
                    $customer_phone = $address->customer_phone;
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

            // === GỌI TỪ SERVICE ===
            $shippingFee = $shippingService->calculateFee($subTotal); 

            $discountAmount = 0;
            $couponId = null;

            if (session()->has('applied_coupon')) {
                $appliedCoupon = session('applied_coupon');

                if (isset($appliedCoupon['applied_at']) && now()->diffInMinutes($appliedCoupon['applied_at']) > 30) {
                    session()->forget('applied_coupon');
                    throw new \Exception('Thời gian áp dụng mã giảm giá đã hết hạn (chỉ được giữ trong 30 phút). Vui lòng nhập lại mã!');
                }

                $couponCode = $appliedCoupon['code'];
                
                // Trọng tâm chống Hack: Dùng Service đã Inject, tham số $lock = true
                $couponResult = $couponService->validateAndCalculate($couponCode, $subTotal, $user->id, true);
                
                if ($couponResult['success']) {
                    $discountAmount = $couponResult['discount_amount']; // Lấy tiền giảm CHUẨN từ Backend tính toán lại
                    $couponId = $couponResult['coupon_id'];
                } else {
                    throw new \Exception('Mã giảm giá bạn đang dùng đã không còn hợp lệ: ' . $couponResult['msg']);
                }
            }

            $totalAmount = $subTotal + $shippingFee - $discountAmount;
            if ($totalAmount < 0) $totalAmount = 0; 

            $orderCode = 'PB' . strtoupper(substr(md5(uniqid()), 0, 13));

            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $user->id, // === ĐÃ DỌN DẸP: Bỏ ?? null ===
                'coupon_id' => $couponId,              
                'discount_amount' => $discountAmount,  
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

            // Trừ Coupon vào trong Transaction
            if ($couponId) {
                \App\Models\Coupon::where('id', $couponId)->decrement('quantity', 1);
                
                // === ĐÃ DỌN DẸP: Bỏ if (Auth::check()) ===
                DB::table('coupon_user')->insert([
                    'coupon_id' => $couponId,
                    'user_id' => $user->id,
                    'used_at' => now()
                ]);
            }

            // Lưu dữ liệu vào DB
            DB::commit(); 

            // Xóa Session Coupon sau khi đã Commit thành công
            if ($couponId) {
                session()->forget('applied_coupon');
            }

            // ==========================================
            // NẾU LÀ VNPAY: CHUYỂN HƯỚNG SANG CỔNG THANH TOÁN
            // ==========================================
            if ($request->payment_method == 'vnpay') {
                $vnp_Url = env('VNP_URL', "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html");
                $vnp_Returnurl = env('VNP_RETURN_URL', url('/checkout/vnpay-return'));
                $vnp_TmnCode = env('VNP_TMN_CODE', "334KPU27");
                $vnp_HashSecret = env('VNP_HASH_SECRET', "Q4EXRFYNPBWN6T1LSKLHIP3VCCTMGWMA");

                $vnp_TxnRef = $order->order_code;
                $vnp_OrderInfo = $order->order_code; 
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = $order->total_amount * 100; 
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
                // === ĐÃ DỌN DẸP: Không cần check 'guest_' nữa ===
                if ($order->customer_email) {
                    Mail::to($order->customer_email)->send(new OrderSuccessMail($order));
                }
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

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET', "Q4EXRFYNPBWN6T1LSKLHIP3VCCTMGWMA");
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
            if ($inputData['vnp_ResponseCode'] == '00') {
                if ($order && $order->payment_status == 'unpaid') {
                    $order->update(['payment_status' => 'paid']);
                    
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_gateway' => 'vnpay',
                        'transaction_code' => $vnp_TransactionNo,
                        'payment_status' => 'success',
                        'amount' => $order->total_amount,
                        'status' => 'Thanh toán VNPay thành công',
                    ]);
                    
                    try {
                        // === ĐÃ DỌN DẸP: Không cần check 'guest_' nữa ===
                        if ($order->customer_email) {
                            Mail::to($order->customer_email)->send(new OrderSuccessMail($order));
                        }
                    } catch (\Throwable $e) {
                        \Log::error('Lỗi gửi mail VNPay: ' . $e->getMessage());
                    }
                }
                
                session()->forget('cart'); 
                return redirect()->route('checkout.success', $order_code)->with('success', 'Thanh toán qua VNPay thành công!');
                
            } else {
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
                        'status' => 'Giao dịch thất bại/Hủy (Mã lỗi: ' . $inputData['vnp_ResponseCode'] . ')',
                    ]);

                    // === HOÀN MÃ GIẢM GIÁ ===
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

                return redirect()->route('checkout.index')->with('error', 'Giao dịch thanh toán VNPAY thất bại hoặc bị hủy. Đơn hàng tạm thời đã bị hủy!');
            }
        } else {
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

                // === HOÀN MÃ GIẢM GIÁ ===
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
            return redirect()->route('checkout.index')->with('error', 'Chữ ký VNPay không hợp lệ. Đơn hàng đã bị hủy!');
        }
    }

    public function success($order_code)
    {
        $order = Order::with('items.product')->where('order_code', $order_code)->firstOrFail();
        return view('client.checkout.success', compact('order'));
    }
}
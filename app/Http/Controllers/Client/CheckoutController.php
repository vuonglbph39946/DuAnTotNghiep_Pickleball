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
use App\Mail\OrderSuccessMail; // ĐÃ THÊM THƯ VIỆN MAIL
use Illuminate\Support\Facades\Mail; // ĐÃ THÊM THƯ VIỆN MAIL

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
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $addresses = $user->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        }

        return view('client.checkout.index', compact('cart', 'subTotal', 'shippingFee', 'totalAmount', 'addresses'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) return back()->with('error', 'Giỏ hàng rỗng hoặc phiên đã hết hạn.');

        $rules = [ 'payment_method' => 'required|in:cod,momo' ];
        $messages = [
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'email.required' => 'Vui lòng nhập Email liên hệ.',
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'ward_id.required' => 'Vui lòng chọn Phường/Xã.',
            'specific_address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
        ];

        if (Auth::check()) {
            if ($request->address_type == 'new') {
                $rules['customer_name'] = 'required|string|max:255';
                $rules['customer_phone'] = ['required', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'];
                $rules['email'] = 'required|email';
                $rules['province_id'] = 'required';
                $rules['district_id'] = 'required';
                $rules['ward_id'] = 'required';
                $rules['specific_address'] = 'required';
            } else {
                $rules['address_id'] = 'required|exists:user_addresses,id';
            }
        } else {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_phone'] = ['required', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'];
            $rules['email'] = 'required|email';
            $rules['province_id'] = 'required';
            $rules['district_id'] = 'required';
            $rules['ward_id'] = 'required';
            $rules['specific_address'] = 'required';
        }

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction(); 

            $userId = Auth::check() ? Auth::id() : null;
            $customerName = ''; $customerPhone = ''; $customerEmail = '';
            $provinceId = ''; $provinceName = '';
            $districtId = ''; $districtName = '';
            $wardId = ''; $wardName = '';
            $specificAddress = '';

            if (Auth::check() && $request->address_type != 'new') {
                $address = Auth::user()->addresses()->findOrFail($request->address_id);
                $customerName = $address->customer_name;
                $customerPhone = $address->customer_phone;
                $customerEmail = Auth::user()->email; 
                $provinceId = $address->province_id;
                $provinceName = $address->province_name;
                $districtId = $address->district_id;
                $districtName = $address->district_name;
                $wardId = $address->ward_id;
                $wardName = $address->ward_name;
                $specificAddress = $address->specific_address;
            } else {
                $customerName = $request->customer_name;
                $customerPhone = $request->customer_phone;
                $customerEmail = $request->email;
                $provinceId = $request->province_id;
                $provinceName = $request->province_name;
                $districtId = $request->district_id;
                $districtName = $request->district_name;
                $wardId = $request->ward_id;
                $wardName = $request->ward_name;
                $specificAddress = $request->specific_address;

                if (Auth::check()) {
                    \App\Models\UserAddress::create([
                        'user_id' => $userId,
                        'customer_name' => $customerName,
                        'customer_phone' => $customerPhone,
                        'province_id' => $provinceId,
                        'province_name' => $provinceName,
                        'district_id' => $districtId,
                        'district_name' => $districtName,
                        'ward_id' => $wardId,
                        'ward_name' => $wardName,
                        'specific_address' => $specificAddress,
                        'is_default' => Auth::user()->addresses()->count() == 0 ? true : false
                    ]);
                }
            }

            $realSubTotal = 0;
            $orderItemsData = [];

            foreach ($cart as $cartKey => $item) {
                $product = Product::where('id', $item['product_id'])->where('status', 1)->lockForUpdate()->first();
                if (!$product) throw new \Exception("Sản phẩm {$item['name']} đã ngừng kinh doanh.");

                $price = $product->sale_price ?? $product->price;
                $stock = $product->stock;

                if ($item['variant_id']) {
                    $variant = ProductVariant::where('id', $item['variant_id'])->where('product_id', $product->id)->lockForUpdate()->first();
                    if (!$variant) throw new \Exception("Phân loại không hợp lệ.");
                    $stock = $variant->stock;
                    $price = $variant->sale_price ?? $variant->price;
                }

                if ($item['quantity'] > $stock) throw new \Exception("Chỉ còn {$stock} sản phẩm.");

                $realSubTotal += $price * $item['quantity'];

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $item['variant_id'],
                    'variant_info' => $item['variant_info'],
                    'price' => $price,
                    'quantity' => $item['quantity'],
                ];
            }

            $shippingFee = $realSubTotal > 500000 ? 0 : 30000;
            $realTotalAmount = $realSubTotal + $shippingFee;

            $order = Order::create([
                'order_code' => 'PB' . strtoupper(uniqid()),
                'user_id' => $userId, 
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'province_id' => $provinceId,
                'province_name' => $provinceName,
                'district_id' => $districtId,
                'district_name' => $districtName,
                'ward_id' => $wardId,
                'ward_name' => $wardName,
                'specific_address' => $specificAddress,
                'total_amount' => $realTotalAmount,
                'shipping_fee' => $shippingFee,
                'payment_status' => 'unpaid',
                'payment_method' => $request->payment_method,
                'order_status' => 'pending',
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

            session()->forget('cart'); 
            DB::commit(); 

            // =======================================================
            // ĐÃ FIX: LỆNH GỬI MAIL VÀ CHUYỂN TRANG XÁC NHẬN Ở ĐÂY
            // =======================================================
            try {
                Mail::to($order->customer_email)->send(new OrderSuccessMail($order));
            } catch (\Throwable $e) {
                // Ghi log lỗi vào file storage/logs/laravel.log nếu cấu hình mail sai
                \Log::error('Lỗi gửi mail: ' . $e->getMessage());
            }

            return redirect()->route('checkout.success', $order->order_code);

        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ĐÃ THÊM HÀM XỬ LÝ TRANG ĐẶT HÀNG THÀNH CÔNG (SUCCESS)
    public function success($order_code)
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();
        return view('client.checkout.success', compact('order'));
    }
}
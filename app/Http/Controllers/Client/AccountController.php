<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\UserAddress;
use App\Models\Order; 
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\OrderStatusLog;

class AccountController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ TRANG TÀI KHOẢN (CÓ TÌM KIẾM & LỌC)
    // ==========================================
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        
        // ĐÃ FIX: Gom đơn hàng theo user_id HOẶC customer_email
        // ĐÃ TỐI ƯU: Thêm 'reviews' vào mảng Eager Loading để fix N+1 Query cho nút Đánh giá
        $query = Order::with(['items.product', 'items.variant', 'reviews'])
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('customer_email', $user->email);
            });

        // 1. Lọc theo trạng thái (Tab)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('order_status', $request->status);
        }

        // 2. Tìm kiếm theo Mã đơn hoặc Tên sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function($qProd) use ($search) {
                      $qProd->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $currentStatus = $request->status ?? 'all';
        $searchTerm = $request->search ?? '';

        return view('client.account.index', compact('user', 'addresses', 'orders', 'currentStatus', 'searchTerm'));
    }

    // ==========================================
    // XEM CHI TIẾT ĐƠN HÀNG
    // ==========================================
    public function showOrder($order_code)
    {
        $user = Auth::user();
        
        // ĐÃ FIX: Cho phép xem nếu khớp ID HOẶC khớp Email
        // ĐÃ TỐI ƯU: Thêm 'reviews' để tránh N+1 nếu trong view chi tiết cũng dùng đến
        $order = Order::with(['items.product', 'items.variant', 'reviews', 'statusLogs' => function($q) {
            $q->orderBy('id', 'desc');
        }])
        ->where(function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('customer_email', $user->email);
        })
        ->where('order_code', $order_code)
        ->firstOrFail();

        return view('client.account.order_detail', compact('order'));
    }

    // ==========================================
    // 2. CẬP NHẬT THÔNG TIN CÁ NHÂN (TÊN, SĐT)
    // ==========================================
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ (VD: 0987654321).'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success_profile', 'Đã cập nhật thông tin cá nhân!');
    }

    // ==========================================
    // 3. ĐỔI MẬT KHẨU
    // ==========================================
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'Mật khẩu mới không được trùng mật khẩu hiện tại.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success_password', 'Đổi mật khẩu thành công!');
    }

    // ==========================================
    // 4. THÊM ĐỊA CHỈ MỚI
    // ==========================================
    public function storeAddress(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'],
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
            'specific_address' => 'required|string|max:255',
        ], [
            'customer_name.required' => 'Vui lòng nhập tên người nhận.',
            'customer_phone.required' => 'Vui lòng nhập SĐT người nhận.',
            'customer_phone.regex' => 'SĐT không hợp lệ.',
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'ward_id.required' => 'Vui lòng chọn Phường/Xã.',
            'specific_address.required' => 'Vui lòng nhập số nhà, tên đường.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $is_default = $request->has('is_default') ? true : false;

        if ($user->addresses()->count() == 0) {
            $is_default = true;
        }

        DB::beginTransaction();
        try {
            if ($is_default) {
                $user->addresses()->update(['is_default' => false]);
            }

            UserAddress::create([
                'user_id' => $user->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'province_id' => $request->province_id,
                'province_name' => $request->province_name, 
                'district_id' => $request->district_id,
                'district_name' => $request->district_name,
                'ward_id' => $request->ward_id,
                'ward_name' => $request->ward_name,
                'specific_address' => $request->specific_address,
                'is_default' => $is_default
            ]);

            DB::commit();
            return back()->with('success_address', 'Thêm địa chỉ thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_address', 'Lỗi hệ thống, vui lòng thử lại!');
        }
    }

    // ==========================================
    // 5. ĐẶT ĐỊA CHỈ LÀM MẶC ĐỊNH
    // ==========================================
    public function setDefaultAddress($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        DB::beginTransaction();
        try {
            $user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
            
            DB::commit();
            return back()->with('success_address', 'Đã thay đổi địa chỉ mặc định!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_address', 'Lỗi hệ thống!');
        }
    }

    // ==========================================
    // 6. XÓA ĐỊA CHỈ
    // ==========================================
    public function destroyAddress($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $address = $user->addresses()->findOrFail($id);

        if ($address->is_default && $user->addresses()->count() > 1) {
            return back()->with('error_address', 'Không thể xóa! Vui lòng chọn địa chỉ khác làm mặc định trước khi xóa địa chỉ này.');
        }

        $address->delete();

        if ($user->addresses()->count() > 0 && !$user->addresses()->where('is_default', true)->exists()) {
            $latestAddress = $user->addresses()->latest()->first();
            if ($latestAddress) {
                $latestAddress->update(['is_default' => true]);
            }
        }

        return back()->with('success_address', 'Đã xóa địa chỉ!');
    }
    
   // ==========================================
    // XỬ LÝ HỦY ĐƠN HÀNG (PHÍA KHÁCH HÀNG)
    // ==========================================
   public function cancelOrder(Request $request, $order_code)
    {
        // 1. Bắt buộc khách hàng phải chọn hoặc nhập lý do hủy đơn
        $request->validate([
            'cancel_reason' => 'required|string|max:500'
        ], [
            'cancel_reason.required' => 'Vui lòng nhập hoặc chọn lý do bạn muốn hủy đơn hàng này.'
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 2. Tìm đơn hàng bảo mật theo đúng mã order_code (ĐÃ FIX TẠI ĐÂY)
            $order = Order::where('order_code', $order_code)
                          ->where(function($q) {
                              $q->where('user_id', Auth::id())
                                ->orWhere('customer_email', Auth::user()->email);
                          })
                          ->lockForUpdate()
                          ->firstOrFail();

            // 3. Kiểm tra điều kiện trạng thái: Chỉ cho phép gửi yêu cầu khi đơn ở trạng thái pending hoặc confirmed
            if (!in_array($order->order_status, ['pending', 'confirmed'])) {
                throw new \Exception('Không thể thao tác! Chỉ có thể gửi yêu cầu hủy khi đơn hàng ở trạng thái Chờ xác nhận hoặc Đã xác nhận.');
            }

            // 4. Đồng nhất chuyển đổi trạng thái sang "Yêu cầu hủy" (cancel_requested)
            $order->order_status = 'cancel_requested';
            $order->cancel_reason = $request->input('cancel_reason');
            $order->save();

            // 5. Ghi nhật ký lịch sử chuyển trạng thái hệ thống
            \App\Models\OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'cancel_requested',
                'created_at' => now()
            ]);

            $message = 'Đã gửi yêu cầu hủy đơn hàng thành công! Vui lòng chờ Shop kiểm tra và duyệt yêu cầu.';

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success_order', $message);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error_order', $e->getMessage());
        }
    }

    public function receiveOrder($order_code)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = Auth::user();
            
            // ĐÃ FIX: Nhận hàng nếu khớp ID HOẶC Email
            $order = Order::where('order_code', $order_code)
                          ->where(function($q) use ($user) {
                              $q->where('user_id', $user->id)
                                ->orWhere('customer_email', $user->email);
                          })
                          ->lockForUpdate()
                          ->firstOrFail();

            if ($order->order_status !== 'shipping') {
                throw new \Exception('Chỉ có thể xác nhận khi đơn hàng đang được giao!');
            }

            $order->order_status = 'completed';

            if ($order->payment_method === 'cod' && $order->payment_status === 'unpaid') {
                $order->payment_status = 'paid';
                
                \App\Models\Payment::create([
                    'order_id' => $order->id,
                    'payment_gateway' => 'cod',
                    'transaction_code' => 'COD_' . time(),
                    'payment_status' => 'success',
                    'amount' => $order->total_amount,
                    'status' => 'Khách đã thanh toán tiền mặt khi nhận hàng'
                ]);
            }

            $order->save();

            \App\Models\OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'completed',
                'created_at' => now()
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success_order', 'Cảm ơn bạn đã xác nhận. Chúc bạn trải nghiệm sản phẩm vui vẻ!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error_order', $e->getMessage());
        }
    }
}
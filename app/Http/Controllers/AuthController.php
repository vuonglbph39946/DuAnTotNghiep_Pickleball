<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order; // ĐÃ THÊM: Model Order để thao tác đồng bộ
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin(Request $request) {
        if ($request->has('redirect')) {
            session(['url.intended' => $request->query('redirect')]);
        } else {
            session()->forget('url.intended');
        }
        return view('client.auth.login'); 
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('email'))->withErrors([
                'email' => "Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau {$seconds} giây."
            ]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            // ========================================================
            // ĐÃ THÊM: ĐỒNG BỘ ĐƠN HÀNG VÃNG LAI KHI ĐĂNG NHẬP
            // ========================================================
            $user = Auth::user();
            Order::where('customer_email', $user->email)
                 ->whereNull('user_id')
                 ->update(['user_id' => $user->id]);

            if (session()->has('url.intended')) {
                return redirect(session('url.intended'))->with('success', 'Đăng nhập thành công!');
            }
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
        }

        RateLimiter::hit($throttleKey);

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.'
        ]);
    }

    public function showRegister() {
        return view('client.auth.register'); 
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $customerRole = DB::table('roles')->where('name', 'customer')->first();
        if ($customerRole) {
            DB::table('user_role')->insert([
                'user_id' => $user->id,
                'role_id' => $customerRole->id
            ]);
        }

        Auth::login($user);

        // ========================================================
        // ĐÃ THÊM: ĐỒNG BỘ ĐƠN HÀNG VÃNG LAI KHI VỪA TẠO TÀI KHOẢN
        // ========================================================
        Order::where('customer_email', $user->email)
             ->whereNull('user_id')
             ->update(['user_id' => $user->id]);

        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    public function showForgotPassword() {
        return view('client.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này.']);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $resetLink = route('password.reset', ['token' => $token, 'email' => $request->email]);

        Mail::send('client.auth.emails.reset-password', ['resetLink' => $resetLink], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Khôi phục mật khẩu - PBall Store');
        });

        return back()->with('success', 'Một liên kết khôi phục mật khẩu đã được gửi đến email của bạn.');
    }

    public function showResetPassword(Request $request, $token) {
        return view('client.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ], [
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $resetRecord = DB::table('password_reset_tokens')
                         ->where('email', $request->email)
                         ->where('token', $request->token)
                         ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Liên kết không hợp lệ hoặc đã bị thay đổi.']);
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Liên kết đã hết hạn. Vui lòng gửi yêu cầu mới!']);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        if ($request->has('redirect')) {
            return redirect($request->query('redirect'));
        }
        return redirect()->route('home');
    }

    // ===============================================
   // XỬ LÝ ĐĂNG NHẬP KHU VỰC ADMIN (GUARD RIÊNG)
    // ===============================================
    public function showAdminLogin() {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login'); 
    }

    public function adminLogin(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 'admin'])) {
            return redirect()->route('admin.dashboard')->with('success', 'Chào mừng sếp quay trở lại!');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Tài khoản không tồn tại hoặc sai mật khẩu!'
        ]);
    }

    public function adminLogout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
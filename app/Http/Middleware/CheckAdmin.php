<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Kiểm tra ĐÃ ĐĂNG NHẬP bằng cổng 'admin' chưa
        if (Auth::guard('admin')->check()) {
            
            // Nếu đúng là Sếp (Admin) -> Mời vào
            if (Auth::guard('admin')->user()->role === 'admin') {
                return $next($request); 
            }
            
            // Nếu không phải admin -> Đá về trang chủ báo lỗi
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập khu vực Quản trị Hệ thống!');
        }
        
        // 2. Nếu CHƯA ĐĂNG NHẬP cổng admin -> Đá ra form login Admin
        return redirect()->route('admin.login')->withErrors([
            'email' => 'Vui lòng đăng nhập bằng tài khoản Quản trị.'
        ]);
    }
}
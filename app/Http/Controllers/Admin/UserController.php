<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // 1. Hiển thị danh sách và Tìm kiếm
    public function index(Request $request)
    {
        $admin = (object)[
            'full_name' => 'Admin Test', // Tạm thời hardcode giống các file khác của bạn
            'avatar' => 'default-avatar.png'
        ];

        $query = User::latest();

        // Xử lý tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo Role (admin/customer)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();
        $totalUsers = $users->total();

        return view('admin.users.index', compact('users', 'totalUsers', 'admin'));
    }

    // 2. Khóa / Mở khóa tài khoản
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Bảo mật: Không cho phép khóa tài khoản Admin
        if ($user->role === 'admin') {
            return back()->with('error', 'Cảnh báo: Không thể khóa tài khoản Quản trị viên!');
        }

        // Đảo ngược trạng thái (1 thành 0, 0 thành 1)
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        $message = $user->status == 1 ? 'Đã mở khóa tài khoản thành công!' : 'Đã khóa tài khoản khách hàng!';
        
        return back()->with('success', $message);
    }
}
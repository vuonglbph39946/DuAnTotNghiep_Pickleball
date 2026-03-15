<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Client\HomeController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===============================================
// AUTH (LOGIN / REGISTER / LOGOUT)
// ===============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// ===============================================
// ROUTE DÀNH CHO CLIENT (NGƯỜI DÙNG)
// ===============================================
// ĐÃ FIX: Chuyển toàn bộ logic sang HomeController để chuẩn cấu trúc MVC
Route::get('/', [HomeController::class, 'index'])->name('home');


// ===============================================
// ROUTE DÀNH CHO ADMIN (QUẢN TRỊ VIÊN)
// ===============================================
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', function () {
        $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
        return view('admin.dashboard', compact('admin'));
    })->name('dashboard');

    Route::get('/accounts', function () {
        $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
        return view('admin.accounts.index', compact('admin'));
    })->name('accounts.index');

    Route::get('/customers', function () {
        $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
        return view('admin.customers.index', compact('admin'));
    })->name('customers.index');

    // SẢN PHẨM & DANH MỤC & THUỘC TÍNH
    Route::delete('products/image/{id}', [ProductController::class, 'destroyImage'])->name('products.image.destroy');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('attributes', AttributeController::class);

    // ĐƠN HÀNG
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::post('/orders/{id}/status', [OrderStatusController::class, 'updateStatus']);
    Route::post('/orders/{id}/undo', [OrderStatusController::class, 'undo']);

    // CÁC ROUTE KHÁC
    Route::get('/roles', function () {
        $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
        return view('admin.roles.index', compact('admin'));
    })->name('roles.index');

    Route::get('/promotions', function () {
        $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
        return view('admin.promotions.index', compact('admin'));
    })->name('promotions.index');

    Route::get('/news', function () {
        $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
        return view('admin.news.index', compact('admin'));
    })->name('news.index');

    Route::get('/accounts/show', function () {
        $admin = (object)['full_name' => 'Admin Test', 'avatar' => 'default-avatar.png'];
        return view('admin.profile', compact('admin'));
    })->name('profile');

    // ===============================================
    // QUẢN LÝ BANNER (ĐÃ FIX LỖI ROUTE NOT FOUND)
    // ===============================================
    Route::resource('banners', BannerController::class);
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrderController;
use App\Models\OrderStatusLog;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\CategoryController; // <-- KHAI BÁO CONTROLLER DANH MỤC Ở ĐÂY

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Trang mặc định (home)
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
})->name('home');


// Routes Admin (tạm thời không cần đăng nhập)
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.dashboard', compact('admin'));
    })->name('dashboard');

    // Tài khoản
    Route::get('/accounts', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.accounts.index', compact('admin'));
    })->name('accounts.index');

    // Khách hàng
    Route::get('/customers', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.customers.index', compact('admin'));
    })->name('customers.index');

    // Sản phẩm
    Route::get('/products', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.products.index', compact('admin'));
    })->name('products.index');

    // Đơn hàng
    Route::get('/orders', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        $orders = \App\Models\Order::latest()->get();
        return view('admin.orders.index', compact('admin', 'orders'));
    })->name('orders.index');

    // Chi tiết đơn hàng 
    Route::get('/orders/{id}', function ($id) {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        $order = \App\Models\Order::with(['items.product', 'statusLogs'])->findOrFail($id);
        return view('admin.orders.show', compact('admin', 'order'));
    })->name('orders.show');

    // ===============================================
    // ROUTE MỚI: IN VẬN ĐƠN (PDF / MÁY IN)
    // ===============================================
    Route::get('/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');

    /*
    |--------------------------------------------------------------------------
    | STATUS ROUTES (ĐÃ TÁCH CONTROLLER – KHÔNG MẤT CHỨC NĂNG CŨ)
    |--------------------------------------------------------------------------
    */
    Route::post('/orders/{id}/status', [OrderStatusController::class, 'updateStatus']);
    Route::post('/orders/{id}/undo', [OrderStatusController::class, 'undo']);

    // Vai trò
    Route::get('/roles', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.roles.index', compact('admin'));
    })->name('roles.index');

    // Khuyến mãi
    Route::get('/promotions', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.promotions.index', compact('admin'));
    })->name('promotions.index');

    // ===============================================
    // ROUTE DANH MỤC: GỌI CHUẨN ĐẾN CONTROLLER
    // ===============================================
    Route::resource('categories', CategoryController::class);

    // Tin tức
    Route::get('/news', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.news.index', compact('admin'));
    })->name('news.index');

    // RAM
    Route::get('/rams', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.rams.index', compact('admin'));
    })->name('rams.index');

    // Kho
    Route::get('/storages', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.storages.index', compact('admin'));
    })->name('storages.index');

    // Màu sắc
    Route::get('/colors', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.colors.index', compact('admin'));
    })->name('colors.index');

    // Profile admin
    Route::get('/accounts/show', function () {
        $admin = (object)[
            'full_name' => 'Admin Test',
            'avatar' => 'default-avatar.png'
        ];
        return view('admin.profile', compact('admin'));
    })->name('profile');

    // /admin → redirect dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

});
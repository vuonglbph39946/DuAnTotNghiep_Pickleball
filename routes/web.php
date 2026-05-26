<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\DashboardController;

// === ĐÃ THÊM MỚI: Import Controller của Admin ===
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\UserController; // <--- KHAI BÁO USER CONTROLLER Ở ĐÂY

use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\CategoryController as ClientCategoryController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Client\AccountController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;

// === ĐÃ THÊM MỚI: Import Controller của Client ===
use App\Http\Controllers\Client\CouponController as ClientCouponController;
use App\Http\Controllers\Client\ReviewController;

use App\Http\Middleware\CheckAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===============================================
// 1. ROUTE XÁC THỰC (LOGIN / REGISTER / FORGOT PASS)
// ===============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');


// ===============================================
// 2. ROUTE CLIENT (NGƯỜI DÙNG CÔNG KHAI)
// ===============================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [ClientCategoryController::class, 'show']);
Route::get('/product/{slug}', [ClientProductController::class, 'show']);
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/api/search-suggest', [HomeController::class, 'searchSuggest']);
Route::get('/gioi-thieu', function () {
    // Sửa chữ 'about' thành 'client.about' (Tức là chỉ đường cho Laravel vào thư mục client để tìm file)
    return view('client.about'); 
})->name('about');
Route::get('/contact', function () {
    return view('client.contact'); 
})->name('contact');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/render', [CartController::class, 'renderCart'])->name('cart.render');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
 
// VẪN GIỮ 2 ROUTE NÀY Ở NGOÀI ĐỂ VNPAY CÓ THỂ ĐẨY DATA VỀ ĐƯỢC
Route::get('/checkout/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay_return');
Route::get('/checkout/success/{order_code}', [CheckoutController::class, 'success'])->name('checkout.success');

// === ĐÃ THÊM MỚI: Route áp dụng Mã Giảm Giá (AJAX) ===
Route::post('/apply-coupon', [ClientCouponController::class, 'apply'])->name('coupon.apply');


// ===============================================
// 3. ROUTE CLIENT NỘI BỘ (YÊU CẦU ĐĂNG NHẬP)
// ===============================================
Route::middleware(['auth'])->group(function () {
    
    // === ĐÃ CHUYỂN VÀO ĐÂY: Bắt buộc đăng nhập để thanh toán ===
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::get('/account/orders/{order_code}', [AccountController::class, 'showOrder'])->name('account.orders.show');
    Route::post('/account/orders/{order_code}/cancel', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    
    // ĐÃ THÊM ROUTE NHẬN HÀNG Ở ĐÂY
    Route::post('/account/orders/{order_code}/receive', [AccountController::class, 'receiveOrder'])->name('account.orders.receive');
    
    Route::post('/reviews/{product_id}', [ReviewController::class, 'store'])->name('client.reviews.store');
    Route::post('/account/update-profile', [AccountController::class, 'updateProfile'])->name('account.update_profile');
    Route::post('/account/update-password', [AccountController::class, 'updatePassword'])->name('account.update_password');
    Route::post('/account/addresses', [AccountController::class, 'storeAddress'])->name('account.addresses.store');
    Route::post('/account/addresses/{id}/default', [AccountController::class, 'setDefaultAddress'])->name('account.addresses.set_default');
    Route::delete('/account/addresses/{id}', [AccountController::class, 'destroyAddress'])->name('account.addresses.destroy');
});


// ===============================================
// 4. ROUTE ADMIN (QUẢN TRỊ VIÊN)
// ===============================================
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::get('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');

Route::prefix('admin')->middleware([CheckAdmin::class])->name('admin.')->group(function () {

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // ========================================================
    // === ĐÃ SỬA: Chuyển Route Dashboard gọi vào Controller ===
    // ========================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === QUẢN LÝ NGƯỜI DÙNG ===
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle_status');

    Route::get('/accounts', function () {
        return view('admin.accounts.index');
    })->name('accounts.index');

    Route::get('/accounts/show', function () {
        return view('admin.profile');
    })->name('profile');

    Route::get('/customers', function () {
        return view('admin.customers.index');
    })->name('customers.index');

    Route::delete('products/image/{id}', [ProductController::class, 'destroyImage'])->name('products.image.destroy');
    Route::resource('products', ProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('attributes', AttributeController::class);
    Route::resource('banners', BannerController::class);
    
    // === ĐÃ THÊM MỚI: Route CRUD cho Mã giảm giá (Admin) ===
    Route::resource('coupons', AdminCouponController::class);

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders-cancel-requests', [OrderController::class, 'cancelRequests'])->name('orders.cancel_requests');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');

    // QUẢN LÝ ĐÁNH GIÁ (REVIEWS)
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.update_status');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{id}/reply', [AdminReviewController::class, 'reply'])->name('reviews.reply');
    
    // Khai báo Tên cho các Route xử lý logic Trạng thái
    Route::post('/orders/{id}/status', [OrderStatusController::class, 'updateStatus'])->name('orders.update_status');
    
    // === ĐÃ BỔ SUNG: CÁC ROUTE PHỤC VỤ DUYỆT HỦY ĐƠN & HOÀN TIỀN VNPAY ===
    Route::post('/orders/{id}/approve-cancel', [OrderStatusController::class, 'approveCancel'])->name('orders.approve_cancel');
    Route::post('/orders/{id}/reject-cancel', [OrderStatusController::class, 'rejectCancel'])->name('orders.reject_cancel');
    Route::post('/orders/{id}/refund-vnpay', [OrderStatusController::class, 'refundVNPay'])->name('orders.refund_vnpay');
    Route::post('/orders/{id}/undo', [OrderStatusController::class, 'undo'])->name('orders.undo');

    Route::get('/roles', function () {
        return view('admin.roles.index');
    })->name('roles.index');

    Route::get('/promotions', function () {
        return view('admin.promotions.index');
    })->name('promotions.index');

    Route::get('/news', function () {
        return view('admin.news.index');
    })->name('news.index');

});
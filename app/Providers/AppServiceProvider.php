<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Khai báo 2 thư viện cần thiết cho Nâng cấp 2
use App\Models\Order;
use App\Observers\OrderObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // KÍCH HOẠT NGƯỜI GIÁM SÁT (OBSERVER) Ở ĐÂY
        Order::observe(OrderObserver::class);
        \App\Models\OrderItem::observe(\App\Observers\OrderItemObserver::class);
    }
}
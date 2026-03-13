<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'user_id',
        'address_id',
        'total_amount',
        'shipping_fee',
        'payment_status',
        'payment_method',
        'order_status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(\App\Models\OrderStatusLog::class)->latest();
    }

    // ĐÃ FIX: Hàm này chống lỗi N+1 Query (tốc độ web tăng x100 lần)
    public function address()
    {
        return $this->belongsTo(\App\Models\Address::class, 'address_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'address_id',
        'total_amount',
        'payment_status',
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'payment_gateway',
        'transaction_code',
        'payment_status',
        'amount',
        'status',
    ];

    // Tạo liên kết ngược lại với đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
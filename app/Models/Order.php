<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    

    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',    
        'customer_phone',   
        'customer_email',    
        'province_id',       
        'province_name',     
        'district_id',       
        'district_name',     
        'ward_id',           
        'ward_name',         
        'specific_address',  
        'total_amount',
        'shipping_fee',
        'payment_status',
        'payment_method',
        'order_status',
        'note',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(\App\Models\OrderStatusLog::class)->latest();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
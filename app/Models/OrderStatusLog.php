<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $table = 'order_status_logs';
    
    // ĐÃ FIX: Thêm 'created_at' vào đây để Laravel không chặn khi tạo log
    protected $fillable = ['order_id', 'status', 'created_at']; 
    
    // Tắt tự động timestamps vì bảng của anh chỉ có created_at, không có updated_at
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
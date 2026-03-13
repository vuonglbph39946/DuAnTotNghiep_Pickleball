<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    // Chỉ định tên bảng trong database
    protected $table = 'addresses';

    // Khai báo các cột được phép thêm/sửa dữ liệu (dựa theo file SQL của bạn)
    protected $fillable = [
        'user_id',
        'receiver_name',
        'phone',
        'address',
        'ward',
        'district',
        'city'
    ];

    // Móc nối ngược lại: 1 địa chỉ thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
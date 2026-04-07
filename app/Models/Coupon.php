<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    // Chỉ định chính xác tên bảng trong Database
    protected $table = 'coupons';

    // Các cột được phép thêm/sửa dữ liệu (Mass Assignment)
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_discount_value',
        'min_order_value',
        'quantity',
        'max_usage_per_user',
        'start_date',
        'end_date',
        'status'
    ];

    // ĐIỂM QUAN TRỌNG: Ép kiểu (Cast) 2 cột ngày tháng thành đối tượng Carbon
    // Nhờ có cái này thì trong Service sếp mới dùng lệnh $now->lt($coupon->start_date) được
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ==========================================
    // KHAI BÁO CÁC MỐI QUAN HỆ (RELATIONSHIPS)
    // ==========================================

    // 1 Mã giảm giá có thể được dùng trong nhiều Đơn hàng
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Quan hệ nhiều-nhiều với User qua bảng trung gian coupon_user
    // Hữu ích cho phần Thống kê ở trang Admin sau này
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')->withPivot('used_at');
    }
}
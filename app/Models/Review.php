<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Review extends Model
{
    use SoftDeletes;
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'comment',
        'status' // 0: Chờ duyệt, 1: Đã duyệt, 2: Từ chối
    ];

    // =====================================
    // LOCAL SCOPES (Nâng cấp chuẩn Senior)
    // =====================================
    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    // =====================================
    // RELATIONSHIPS
    // =====================================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Lấy danh sách ảnh của đánh giá này
    public function images()
    {
        return $this->hasMany(ReviewImage::class, 'review_id', 'id');
    }

    // Lấy phản hồi của Shop cho đánh giá này
    public function reply()
    {
        return $this->hasOne(ReviewReply::class, 'review_id', 'id');
    }
}
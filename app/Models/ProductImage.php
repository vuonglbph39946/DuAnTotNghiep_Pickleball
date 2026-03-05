<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    // Chỉ định chính xác tên bảng trong Database
    protected $table = 'product_images';

    // Cực kỳ quan trọng: Tắt timestamps vì bảng của anh không có created_at và updated_at
    public $timestamps = false;

    // Các cột được quyền cập nhật
    protected $fillable = [
        'product_id',
        'image_path'
    ];

    // Móc nối ngược lại bảng Product (1 ảnh thuộc về 1 sản phẩm cụ thể)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
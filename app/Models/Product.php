<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    // ĐÃ FIX: Thêm 'sku' vào danh sách này
    protected $fillable = [
        'category_id',
        'sku',        
        'name',
        'slug',
        'price',
        'sale_price',
        'description',
        'stock',
        'status'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ==========================================
    // LIÊN KẾT ĐẾN MODEL PRODUCT IMAGE
    // ==========================================
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    // ==========================================
    // LIÊN KẾT ĐẾN DANH MỤC (1 Sản phẩm thuộc 1 Danh mục)
    // ==========================================
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // ==========================================
    // LIÊN KẾT ĐẾN CÁC BIẾN THỂ CỦA SẢN PHẨM (MỚI)
    // ==========================================
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }
}
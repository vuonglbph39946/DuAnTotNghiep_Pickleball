<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id',
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
    // LIÊN KẾT ĐẾN MODEL PRODUCT IMAGE VỪA TẠO
    // ==========================================
    public function images()
    {
        // Vì ProductImage và Product nằm cùng chung thư mục App\Models 
        // nên Laravel sẽ tự động nhận diện được nhau.
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
}
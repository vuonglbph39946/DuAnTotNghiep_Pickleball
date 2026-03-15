<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock'
    ];

    // ==========================================
    // Biến thể này thuộc về Sản phẩm gốc nào
    // ==========================================
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    // ==========================================
    // Biến thể này được ghép từ những Giá trị nào (VD: 14mm + Màu Xanh)
    // ==========================================
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_values', 'variant_id', 'attribute_value_id');
    }
}
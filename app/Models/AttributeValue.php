<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $table = 'attribute_values';

    protected $fillable = [
        'attribute_id',
        'value',
        'color_code' // <-- Đã thêm trường này để lưu mã màu Hex
    ];

    // ==========================================
    // Giá trị này thuộc về Nhóm Thuộc tính nào
    // ==========================================
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    // ==========================================
    // Nối với Biến thể qua bảng trung gian variant_attribute_values
    // ==========================================
    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_attribute_values', 'attribute_value_id', 'variant_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attributes';

    protected $fillable = [
        'name'
    ];

    // ==========================================
    // 1 Thuộc tính (VD: Màu sắc) có nhiều Giá trị (VD: Đỏ, Xanh)
    // ==========================================
    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'id');
    }
}
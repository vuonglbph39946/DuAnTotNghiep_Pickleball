<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'image',
        'status'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // ==========================================
    // LIÊN KẾT DANH MỤC CHA - CON
    // ==========================================
    // Lấy ra danh mục cha
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Lấy ra các danh mục con
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
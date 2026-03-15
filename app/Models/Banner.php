<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    // Chỉ định chính xác tên bảng trong database
    protected $table = 'banners';

    // Khai báo các cột được phép thêm/sửa dữ liệu (Khớp 100% với file SQL lúc nãy)
    protected $fillable = [
        'title',
        'image_path',
        'link',
        'position',
        'status',
    ];
}
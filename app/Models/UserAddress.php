<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_addresses';
    protected $fillable = [
        'user_id', 'customer_name', 'customer_phone',
        'province_id', 'province_name', 'district_id', 'district_name',
        'ward_id', 'ward_name', 'specific_address', 'is_default'
    ];
    protected $casts = [
        'is_default' => 'boolean',
    ];
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
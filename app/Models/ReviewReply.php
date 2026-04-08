<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    protected $table = 'review_replies';
    protected $fillable = ['review_id', 'admin_id', 'content'];

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogAndComment extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $imageDetailModel,$videoDetailModel,$emojiDetailModel;
    public function images()
    {
        return $this->hasMany($this->imageDetailModel);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function videos()
    {
        return $this->hasMany($this->videoDetailModel);
    }
    public function emojiDetails()
    {
        return $this->hasMany(
            $this->emojiDetailModel,
        );
    }
    public function emojiUsers()
    {
        return $this->hasManyThrough(
            User::class,
            $this->emojiDetailModel,
            'blog_id', // Khóa ngoại trên bảng emoji_blog_details
            'id',      // Khóa chính trên bảng users
            'id',      // Khóa chính trên bảng blogs (đang xét)
            'user_id'  // Khóa ngoại trên bảng emoji_blog_details
        );
    }
}

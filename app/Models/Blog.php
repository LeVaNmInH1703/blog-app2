<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(ImageBlogDetail::class);
    }
    public function videos()
    {
        return $this->hasMany(VideoBlogDetail::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function feedbacks()
    {
        return $this->hasMany(Comment::class);
    }
}

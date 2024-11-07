<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'content',
    ];
    public function getId()
    {
        return $this->id;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function files()
    {
        return $this->hasMany(FileBlog::class, 'blog_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'blog_id');
    }
    public function feelings()
    {
        return $this->hasMany(Comment::class, 'blog_id');
    }
}

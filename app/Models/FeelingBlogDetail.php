<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeelingBlogDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'blog_id',
        'feeling_id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function blog(){
        return $this->belongsTo(Blog::class);
    }
    public function feeling(){
        return $this->belongsTo(Feeling::class);
    }
}

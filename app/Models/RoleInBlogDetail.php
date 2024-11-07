<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleInBlogDetail extends Model
{
    use HasFactory;
    protected $fillable=['user_id','blog_id','role_in_blogs'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function role(){
        return $this->belongsTo(RoleInGroupChat::class);
    }
    public function blog(){
        return $this->belongsTo(Blog::class);
    }
}

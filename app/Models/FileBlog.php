<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileBlog extends Model
{
    use HasFactory;
    protected $fillable=[
        'blog_id',
        'file_name',
    ];
    public function blog(){
        return $this->belongsTo(Blog::class);
    }
}

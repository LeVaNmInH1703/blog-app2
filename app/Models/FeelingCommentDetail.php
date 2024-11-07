<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeelingCommentDetail extends Model
{
    use HasFactory;
    protected $fillable=['user_id','comment_id','feeling_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function comment(){
        return $this->belongsTo(Comment::class);
    }
    public function feeling(){
        return $this->belongsTo(Feeling::class);
    }
}

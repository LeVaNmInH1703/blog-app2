<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmojiCommentDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function comment(){
        return $this->belongsTo(Comment::class);
    }
    public function emoji(){
        return $this->belongsTo(Emoji::class);
    }
}

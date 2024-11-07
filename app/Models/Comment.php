<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable=['user_id','blog_id','content'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function blog(){
        return $this->belongsTo(Blog::class);
    }
    public function feelings(){
        // return $this->belongsToMany(Feeling::class,'feeling_comment_details');
        return $this->belongsToMany(Feeling::class, 'feeling_comment_details', 'comment_id', 'feeling_id')->withPivot('user_id') // Include user_id from the pivot table
            ->join('users', 'feeling_comment_details.user_id', '=', 'users.id') // Join with users table
            ->select('feelings.*', 'users.name as userName','users.id as userId','users.*');
    }
    public function comments(){
        return $this->belongsToMany(Comment::class,'reply_comment_details','comment_id','reply_comment_id');
    }
    public function replyCommentDetail(){
        return $this->belongsTo(ReplyCommentDetail::class,'id','reply_comment_id');
    }
    public function image(){
        return $this->belongsTo(ImageComment::class,'id','comment_id');
    }
}

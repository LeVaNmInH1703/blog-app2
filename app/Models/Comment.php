<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
    public function feedbacks()
    {
        return $this->belongsTo(Feedback::class);
    }
    public function commentReplies()
    {
        /*
        ______________ quan hệ nhiều nhiều
        bảng trung gian table: reply_comment_details
        quan hệ với bảng related: Comment::class
        id của mình trên bảng trung gian foreignPivotKey:'comment_id'
        id của bảng quan hệ trên bảng trung gian relatedPivotKey:'reply_comment_id'
        */
        return $this->belongsToMany(Comment::class, 'reply_comment_details', 'comment_id', 'reply_comment_id');
    }
    public function image()
    {
        return $this->belongsTo(ImageCommentDetail::class, 'id', 'comment_id');
    }
    public function videos()
    {
        return $this->hasMany(VideoCommentDetail::class, 'comment_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyCommentDetail extends Model
{
    use HasFactory;
    protected $fillable=['comment_id','reply_comment_id'];
}

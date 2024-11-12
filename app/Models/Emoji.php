<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emoji extends Model
{
    use HasFactory; // like, dislike
    protected $guarded = [];
    protected $table = 'emojis';
}

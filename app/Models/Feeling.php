<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feeling extends Model
{
    use HasFactory;//heart,like,funny,angry,sad,astonished,loving face
    protected $fillable=['name','alt','src','color_text'];
}

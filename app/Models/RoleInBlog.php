<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleInBlog extends Model
{
    use HasFactory;//auth,viewer,hidden,blocked
    protected $fillable = [
        'name',
    ];
}

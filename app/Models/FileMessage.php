<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileMessage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function message(){
        return $this->belongsTo(Message::class);
    }
}

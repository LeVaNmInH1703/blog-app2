<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id_send');
    }

    public function group()
    {
        return $this->belongsTo(GroupChat::class,'group_id_receive');
    }
    public function files(){
        return $this->hasMany(FileMessage::class);
    }
}

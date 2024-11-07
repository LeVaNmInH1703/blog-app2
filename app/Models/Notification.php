<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable=['content','user_id_send','user_id_receive','link','isSaw',"key_word"];
    public function user(){
        return $this->belongsTo(User::class,'user_id_send');
    }
}

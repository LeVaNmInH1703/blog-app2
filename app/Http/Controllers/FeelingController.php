<?php

namespace App\Http\Controllers;

use App\Models\Feeling;
use Illuminate\Http\Request;

class FeelingController extends Controller
{
    public function __construct(){
        if(Feeling::all()->count()==0)
            foreach (['like','heart', 'loving face',  'funny', 'astonished', 'angry', 'sad'] as $name){
                Feeling::create([
                    'name'=>$name
                ]);
            }
    }
}

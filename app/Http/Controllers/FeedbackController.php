<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct(){
        if(Feedback::all()->count()==0)
            foreach (['like','heart', 'loving face',  'funny', 'astonished', 'angry', 'sad'] as $name){
                Feedback::create([
                    'name'=>$name
                ]);
            }
    }
}

<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Feeling;
use App\Models\FeelingCommentDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeelingCommentDetail>
 */
class FeelingCommentDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do{
            $user_id = User::inRandomOrder()->first()->id;
            $comment_id = Comment::inRandomOrder()->first()->id;
        }
        while (FeelingCommentDetail::where([
            ['user_id',$user_id],
            ['comment_id',$comment_id],
        ])->exists());  
        return [
            'comment_id'=>$comment_id,
            'user_id'=>$user_id,
            'feeling_id'=>Feeling::inRandomOrder()->first()->id,
        ];
    }
}

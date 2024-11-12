<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Feedback;
use App\Models\FeedbackCommentDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeedbackCommentDetail>
 */
class FeedbackCommentDetailFactory extends Factory
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
        while (FeedbackCommentDetail::where([
            ['user_id',$user_id],
            ['comment_id',$comment_id],
        ])->exists());  
        return [
            'comment_id'=>$comment_id,
            'user_id'=>$user_id,
            'feedback_id'=>Feedback::inRandomOrder()->first()->id,
        ];
    }
}

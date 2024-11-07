<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\Feeling;
use App\Models\FeelingBlogDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeelingBlogDetail>
 */
class FeelingBlogDetailFactory extends Factory
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
            $blog_id = Blog::inRandomOrder()->first()->id;
        }
        while (FeelingBlogDetail::where([
            ['user_id',$user_id],
            ['blog_id',$blog_id],
        ])->exists());   
        return [
            'user_id' => $user_id,
            'blog_id'=>$blog_id,
            'feeling_id'=>Feeling::inRandomOrder()->first()->id,
        ];
    }
}

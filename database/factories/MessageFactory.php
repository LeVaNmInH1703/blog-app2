<?php

namespace Database\Factories;

use App\Models\FriendShips;
use App\Models\GroupChat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $user_id_send=User::inRandomOrder()->first()->id;
        $group_id_receive=GroupChat::inRandomOrder()->first()->id;
        return [
            'user_id_send'=>$user_id_send,
            'group_id_receive'=>$group_id_receive,
            'content'=>fake()->paragraph(),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Emoji;
use App\Models\EmojiBlogDetail;
use App\Models\EmojiCommentDetail;
use App\Models\FriendShips;
use App\Models\Message;
use App\Models\ReplyCommentDetail;
use App\Models\User;
use Database\Factories\ReplyCommentDetailFactory;
use Exception;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->makeEmojis();
        $this->createUser();
        $this->friendShip();
        $this->makeBlog();
        // $this->makeEmojiBlogAction();
        // $this->makeComments();

        // $this->makeGroupChat();//chưa làm nên k chạy make message
        // $this->makeMessage();
        
        // $this->makeEmojiCommentAction();
        // $this->makeReplyCommentDetail();
    }
    public function makeEmojis()
    {
        Emoji::factory()->create([
            'name' => 'like',
        ]);
    }
    public function makeReplyCommentDetail()
    {
        for ($i = 1; $i < 100; $i++)
            ReplyCommentDetail::factory()->create();
    }
    public function makeMessage()
    {
        for ($i = 1; $i <= 100; $i++)
            Message::factory()->create();
    }
    public function createUser()
    {
        for ($i = 1; $i < 10; $i++) {
            $temp = $i . '@gmail.com';
            $temp2 = 'avatar' . ($i % 7 + 1) . '.png';
            User::factory()->create([
                'email' => $temp,
                'name' => $temp,
                'password' => $temp,
                'url_avatar' => asset('images_resize/') . '/' . $temp2
            ]);
        }
    }
    public function friendShip()
    {
        for ($i = 1; $i < 50; $i++) {
            FriendShips::factory()->create();
            //lưu ý ->count() sẽ k phải create one mà là create all nên sẽ không thể lấy giá trị bản ghi trước
        }
    }
    public function makeBlog()
    {
        Blog::factory()->count(30)->create([
            'user_id' => User::inRandomOrder()->first()->id,
            'content'=>Fake()->paragraph(1),
        ]);
    }

    public function makeEmojiBlogAction()
    {
        for ($i = 1; $i <= 60; $i++) {
            EmojiBlogDetail::factory()->create();
        }
    }
    public function makeComments()
    {
        Comment::factory()->count(200)->create();
    }
    public function makeEmojiCommentAction()
    {
        for ($i = 1; $i <= 100; $i++) {
            EmojiCommentDetail::factory()->create();
        }
    }
}

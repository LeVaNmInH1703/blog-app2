<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Feedback;
use App\Models\FeedbackBlogDetail;
use App\Models\FeedbackCommentDetail;
use App\Models\FriendShips;
use App\Models\Message;
use App\Models\ReplyCommentDetail;
use App\Models\User;
use Database\Factories\ReplyCommentDetailFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->makeFeedbacks();
        $this->createUser();
        $this->friendShip();
        $this->makeBlog();
        $this->makeFeedbackBlogAction();
        $this->makeComments();
        // $this->makeGroupChat();//chưa làm nên k chạy make message
        // $this->makeMessage();
        $this->makeFeedbackCommentAction();
        $this->makeReplyCommentDetail();
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
        for ($i = 1; $i < 30; $i++) {
            FriendShips::factory()->create();
            //lưu ý ->count() sẽ k phải create one mà là create all nên sẽ không thể lấy giá trị bản ghi trước
        }
    }
    public function makeBlog()
    {
        Blog::factory()->count(30)->create();
    }
    public function makeFeedbacks()
    {
        Feedback::factory()->create([
            'icon' => "",
            'name'=>'like'
        ]);
    }
    public function makeFeedbackBlogAction()
    {
        for ($i = 1; $i <= 60; $i++) {
            FeedbackBlogDetail::factory()->create();
        }
    }
    public function makeComments()
    {

        Comment::factory()->count(200)->create();
    }
    public function makeFeedbackCommentAction()
    {
        for ($i = 1; $i <= 100; $i++) {
            FeedbackCommentDetail::factory()->create();
        }
    }
}

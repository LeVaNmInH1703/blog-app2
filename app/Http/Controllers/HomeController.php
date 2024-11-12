<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Emoji;
use App\Models\EmojiBlogDetail;
use App\Models\User;
use App\Notifications\EventNotification;
use App\Notifications\SimpleNotification;
use App\Services\BlogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $blogService;
    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }
    public function about()
    {
        return view("pages.about");
    }
    public function index(){
        // check verify mail
        if (!Auth::user()->email_verified_at)
            return redirect()->route('waitToVerifyEmail');
        // get blogs
        $blogs=$this->blogService->getAndSetSomeBlog();
        return view('pages.home', compact('blogs'));
    }
    // public function index()
    // {
    //     if (!Auth::user()->email_verified_at)
    //         return redirect()->route('waitToVerifyEmail');
    //     // dd(Auth::user()->temp);
    //     $blogs = $this->blogRepository->takeAndSetSomeBlog();
    //     // chèn blog mới tạo vào trước
    //     $newBlog = session('newBlog');
    //     if ($newBlog) {
    //         $blogs = $this->blogRepository->takeAndSetSomeBlog(5, [$newBlog->getId()]);
    //         $blogs->prepend($newBlog);
    //         session()->forget('newBlog');
    //     }
    //     return view("pages.home", compact('blogs'));
    // }
    public function test(){
        //____________ gửi sms qua vonage ____________ 
        // $basic  = new \Vonage\Client\Credentials\Basic("adc7c688", "ykcOX9FKR5Gf3KDa");
        // $client = new \Vonage\Client($basic);
        // $response = $client->sms()->send(
        //     new \Vonage\SMS\Message\SMS("84394657140", 'blog app 2', 'A text message sent using the Nexmo SMS API')
        // );
        
        // $message = $response->current();
        
        // if ($message->getStatus() == 0) {
        //     echo "The message was sent successfully\n";
        // } else {
        //     echo "The message failed with status: " . $message->getStatus() . "\n";
        // }
        //____________ một cách khác để gửi mail đơn giản ____________ 
        // $user = User::find(Auth::id());
        // $user->notify(new SimpleNotification('Bạn có một thông báo mới!'));
        // return response()->json(['message' => 'Notification sent successfully!']);

        //____________ test notify ____________ 
        // $user = User::find(1);
        // $user->notify(new EventNotification('Bạn có một sự kiện mớiiiiiiiiiii!'));

        //____________ test service container ____________ 
        dd(app(UserController::class)->index());
    }
}

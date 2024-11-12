<?php

namespace App\Http\Controllers;

use App\Events\BlogEvent;
use App\Events\HasNewNotificationEvent;
use App\Events\PostEvent;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Feedback;
use App\Models\FeedbackBlogDetail;
use App\Models\FeedbackCommentDetail;
use App\Models\ImageBlog;
use App\Models\ImageBlogDetail;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\BlogRepository;
use App\Repositories\BlogRepositoryInterface;
use App\Repositories\CommentRepository;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\FeedbackRepository;
use App\Repositories\FeedbackRepositoryInterface;
use App\View\Components\CountFeedbackComponent;
use App\View\Components\GetFeedbackComponent;
use Barryvdh\Debugbar\Twig\Extension\Debug;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BlogController extends Controller
{
    public function __construct()
    {
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
    // public function createBlogGetView()
    // {
    //     return view('pages.createPost');
    // }
    // public function loadMoreBlog(Request $request)
    // {
    //     $loadedBlogs = $request->input('loadedBlogs', []); //k cho load lại các blog đã render để tránh k listen đc event bên cli
    //     $blogs = $this->blogRepository->takeAndSetSomeBlog(5, $loadedBlogs);
    //     $result = '';
    //     foreach ($blogs as $blog)
    //         $result .= view('components/blog-component', compact('blog'))->render();
    //     return $result;
    // }
    // public function blogDetail($blog)
    // {
    //     $blog = $this->blogRepository->get($blog);
    //     if (!$blog) abort(404);
    //     $this->blogRepository->setBlog($blog);
    //     $blog->comments = $blog->comments->sortByDesc('created_at');
    //     foreach ($blog->comments as $comment)
    //         if ($comment->replyCommentDetail == null) //comment này có rep comment nào k
    //             $this->commentRepository->setComment($comment); //k

    //     return view('pages.blogDetail', compact('blog'));
    // }

    // public function findObjByName($name, $objID)
    // {
    //     if ($name == 'blog') {
    //         return $this->blogRepository->find($objID);
    //     } else if ($name == 'comment') {
    //         return  $this->commentRepository->find($objID);
    //     }
    // }
    // public function toggleFeedback($name, $objID)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment'))
    //         abort(404);
    //     $getFeedbackRender = '';
    //     if ($this->takeFeedback($name, $obj)->exists()) {
    //         $this->useTransaction(function () use ($name, $obj) {
    //             $this->takeFeedback($name, $obj)->delete();
    //         });
    //         $getFeedbackRender = $this->feedbackRepository->getView();
    //     } else {
    //         $feedback = Feedback::where('name', 'heart')->first();
    //         $this->createFeedback($name, $obj, $feedback);
    //         $getFeedbackRender = $this->feedbackRepository->getView($feedback);
    //     }
    //     return response()->json([
    //         'countFeedbackRender' => str($this->countFeedback($name, $objID)),
    //         'getFeedbackRender' => str($getFeedbackRender),
    //         'test' => 'aaaaaaaaaaaaaa'
    //     ]);
    // }
    // public function countFeedback($name, $objID)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment')) abort(404);
    //     if ($name == 'blog') {
    //         $obj->clientFeedback = FeedbackBlogDetail::where([['user_id', Auth::id()], ['blog_id', $obj->id]])->first();
    //         $size = 18;
    //     } else if ($name == 'comment') {
    //         $obj->clientFeedback = FeedbackCommentDetail::where([['user_id', Auth::id()], ['comment_id', $obj->id]])->first();
    //         $size = 12;
    //     }
    //     $obj->countFeedback = $obj->feedbacks->count();
    //     return (new CountFeedbackComponent($obj, $size))->render();
    // }

    // public function makeFeedback($name, $objID, Feedback $feedback)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment') || !$feedback) {
    //         //blog k có hoặc người dùng bị chặn
    //         abort(404);
    //     }

    //     if ($this->takeFeedback($name, $obj)->exists()) {
    //         //update feedback
    //         $this->updateFeedback($name, $obj, $feedback);
    //     } else {
    //         $this->createFeedback($name, $obj, $feedback);
    //         //create new feedback
    //     }
    //     return response()->json([
    //         'countFeedbackRender' => str($this->countFeedback($name, $objID)),
    //         'getFeedbackRender' => str($this->feedbackRepository->getView($feedback)),
    //     ]);
    // }
    // public function deleteFeedback($name, $objID)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment')) {
    //         abort(404);
    //     }
    //     $this->useTransaction(function () use ($name, $obj) {
    //         if ($name == 'blog') {
    //             $this->blogRepository->deleteFeedback($obj);
    //         } else if ($name == 'comment') {
    //             $this->commentRepository->deleteFeedback($obj);
    //         }
    //     });
    //     return redirect()->back();
    // }
    // public function updateFeedback($name, $obj, Feedback $feedback)
    // {
    //     if ($name == 'blog') {
    //         $this->blogRepository->updateFeedback($obj, $feedback);
    //     } else if ($name == 'comment') {
    //         $this->commentRepository->updateFeedback($obj, $feedback);
    //     }
    // }
    // public function createFeedback($name, $obj, Feedback $feedback)
    // {
    //     $this->createNotify($name, $obj, $feedback);
    //     return $this->useTransaction(function () use ($name, $obj, $feedback) {
    //         if ($name == 'blog') {
    //             FeedbackBlogDetail::create([
    //                 'user_id' => Auth::id(),
    //                 'blog_id' => $obj->id,
    //                 'feedback_id' => $feedback->id
    //             ]);
    //         } else if ($name == 'comment') {
    //             FeedbackCommentDetail::create([
    //                 'user_id' => Auth::id(),
    //                 'comment_id' => $obj->id,
    //                 'feedback_id' => $feedback->id
    //             ]);
    //         }
    //     });
    // }
    // public function createNotify($name, $obj, $feedback)
    // {
    //     if ($obj->user->id == Auth::id()) return;
    //     if ($obj->feedbacks->count() <= 1)
    //         $content = __("public.Someone reacted to your something", ['Someone' => Auth::user()->name, 'something' => $name]);
    //     else
    //         $content = __("public.Someone and some others reacted to your something", ['Someone' => Auth::user()->name, 'some' => $obj->feedbacks->count() - 1, 'something' => $name]);
    //     $content = $content . " " . $this->feedbackRepository->getView($feedback, false, 15);
    //     $notification = Notification::create([
    //         'user_id_send' => Auth::id(),
    //         'content' => $content,
    //         'user_id_receive' => $obj->user->id,
    //         'link' => route('blogDetail', ($name == 'comment' ? $obj->blog->id : $obj->id)),
    //         'key_word' => $name . '-' . $obj->id
    //     ]);
    //     event(new HasNewNotificationEvent($notification));
    // }
    // public function takeFeedback($name, $obj)
    // {
    //     if ($name == 'blog') {
    //         return $this->blogRepository->takeFeedback($obj);
    //     } else if ($name == 'comment') {
    //         return $this->commentRepository->takeFeedback($obj);
    //     }
    // }
    // public function createBlog(Request $request)
    // {
    //     $privacy = $request->input('privacy');
    //     $datetime = $request->input('datetime');
    //     if ($request->input('content') == "" && $request->files == "")
    //         return redirect()->back()->with(['message' => __('public.Please write something !')]);
    //     $request->validate([
    //         'files.*' => 'file',
    //     ]);
    //     $newBlog = new Blog();
    //     $result = $this->useTransaction(function () use ($request, &$newBlog) {
    //         $newBlog = Blog::create([
    //             'content' => $request->input('content'),
    //             'user_id' => Auth::id(),
    //             'scheduled_at' => $datetime ?? now()
    //         ]);
    //         $files = $request->file('files');
    //         if ($files) {
    //             foreach ($files as $file) {
    //                 $mimeType = $file->getMimeType();
    //                 if ($file) {

    //                     if (str_starts_with($mimeType, 'image/')) {
    //                         $fileBlogName = 'blog_image_' . $newBlog->id . Str::random(20) . '.' . $file->extension();

    //                         // resize chú ý dùng đúng driver
    //                         $manager = new ImageManager(new Driver());
    //                         // $manager->read($image)->resize(250, 250)->save(public_path('images_resize') .'/'. $fileBlogName);
    //                         $temp = $manager->read($file);
    //                         if ($temp->width() > 640)
    //                             $temp->resize(640, 640 * $temp->height() / $temp->width());

    //                         $temp->save(public_path('images_resize') . '/' . $fileBlogName);

    //                         //move
    //                         $file->move(public_path('images'), $fileBlogName);
    //                         ImageBlogDetail::create([
    //                             'file_name' => $fileBlogName,
    //                             'blog_id' => $newBlog->id
    //                         ]);
    //                     } else if (str_starts_with($mimeType, 'video/')) {
    //                         $fileBlogName = 'blog_video_' . $newBlog->id . Str::random(20) . '.' . $file->extension();
    //                         //move
    //                         $file->move(public_path('videos'), $fileBlogName);

    //                         ImageBlogDetail::create([
    //                             'file_name' => $fileBlogName,
    //                             'blog_id' => $newBlog->id
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //     });
    //     if ($result) {
    //         return redirect()->route('home')->with('message', __('public.Posted'))->with('newBlog', $newBlog);
    //     } else {
    //         return redirect()->back()->with('message', "Error");
    //     }
    // }
}

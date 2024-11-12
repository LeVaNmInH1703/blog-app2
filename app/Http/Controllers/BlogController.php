<?php

namespace App\Http\Controllers;

use App\Events\BlogEvent;
use App\Events\HasNewNotificationEvent;
use App\Events\PostEvent;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Emoji;
use App\Models\EmojiBlogDetail;
use App\Models\EmojiCommentDetail;
use App\Models\ImageBlog;
use App\Models\ImageBlogDetail;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\BlogRepository;
use App\Repositories\BlogRepositoryInterface;
use App\Repositories\CommentRepository;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\EmojiRepository;
use App\Repositories\EmojiRepositoryInterface;
use App\View\Components\CountEmojiComponent;
use App\View\Components\GetEmojiComponent;
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
    // public function toggleEmoji($name, $objID)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment'))
    //         abort(404);
    //     $getEmojiRender = '';
    //     if ($this->takeEmoji($name, $obj)->exists()) {
    //         $this->useTransaction(function () use ($name, $obj) {
    //             $this->takeEmoji($name, $obj)->delete();
    //         });
    //         $getEmojiRender = $this->emojiRepository->getView();
    //     } else {
    //         $emoji = Emoji::where('name', 'heart')->first();
    //         $this->createEmoji($name, $obj, $emoji);
    //         $getEmojiRender = $this->emojiRepository->getView($emoji);
    //     }
    //     return response()->json([
    //         'countEmojiRender' => str($this->countEmoji($name, $objID)),
    //         'getEmojiRender' => str($getEmojiRender),
    //         'test' => 'aaaaaaaaaaaaaa'
    //     ]);
    // }
    // public function countEmoji($name, $objID)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment')) abort(404);
    //     if ($name == 'blog') {
    //         $obj->clientEmoji = EmojiBlogDetail::where([['user_id', Auth::id()], ['blog_id', $obj->id]])->first();
    //         $size = 18;
    //     } else if ($name == 'comment') {
    //         $obj->clientEmoji = EmojiCommentDetail::where([['user_id', Auth::id()], ['comment_id', $obj->id]])->first();
    //         $size = 12;
    //     }
    //     $obj->countEmoji = $obj->emojis->count();
    //     return (new CountEmojiComponent($obj, $size))->render();
    // }

    // public function makeEmoji($name, $objID, Emoji $emoji)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment') || !$emoji) {
    //         //blog k có hoặc người dùng bị chặn
    //         abort(404);
    //     }

    //     if ($this->takeEmoji($name, $obj)->exists()) {
    //         //update emoji
    //         $this->updateEmoji($name, $obj, $emoji);
    //     } else {
    //         $this->createEmoji($name, $obj, $emoji);
    //         //create new emoji
    //     }
    //     return response()->json([
    //         'countEmojiRender' => str($this->countEmoji($name, $objID)),
    //         'getEmojiRender' => str($this->emojiRepository->getView($emoji)),
    //     ]);
    // }
    // public function deleteEmoji($name, $objID)
    // {
    //     $obj = $this->findObjByName($name, $objID);
    //     if (!$obj || ($name != 'blog' && $name != 'comment')) {
    //         abort(404);
    //     }
    //     $this->useTransaction(function () use ($name, $obj) {
    //         if ($name == 'blog') {
    //             $this->blogRepository->deleteEmoji($obj);
    //         } else if ($name == 'comment') {
    //             $this->commentRepository->deleteEmoji($obj);
    //         }
    //     });
    //     return redirect()->back();
    // }
    // public function updateEmoji($name, $obj, Emoji $emoji)
    // {
    //     if ($name == 'blog') {
    //         $this->blogRepository->updateEmoji($obj, $emoji);
    //     } else if ($name == 'comment') {
    //         $this->commentRepository->updateEmoji($obj, $emoji);
    //     }
    // }
    // public function createEmoji($name, $obj, Emoji $emoji)
    // {
    //     $this->createNotify($name, $obj, $emoji);
    //     return $this->useTransaction(function () use ($name, $obj, $emoji) {
    //         if ($name == 'blog') {
    //             EmojiBlogDetail::create([
    //                 'user_id' => Auth::id(),
    //                 'blog_id' => $obj->id,
    //                 'emoji_id' => $emoji->id
    //             ]);
    //         } else if ($name == 'comment') {
    //             EmojiCommentDetail::create([
    //                 'user_id' => Auth::id(),
    //                 'comment_id' => $obj->id,
    //                 'emoji_id' => $emoji->id
    //             ]);
    //         }
    //     });
    // }
    // public function createNotify($name, $obj, $emoji)
    // {
    //     if ($obj->user->id == Auth::id()) return;
    //     if ($obj->emojis->count() <= 1)
    //         $content = __("public.Someone reacted to your something", ['Someone' => Auth::user()->name, 'something' => $name]);
    //     else
    //         $content = __("public.Someone and some others reacted to your something", ['Someone' => Auth::user()->name, 'some' => $obj->emojis->count() - 1, 'something' => $name]);
    //     $content = $content . " " . $this->emojiRepository->getView($emoji, false, 15);
    //     $notification = Notification::create([
    //         'user_id_send' => Auth::id(),
    //         'content' => $content,
    //         'user_id_receive' => $obj->user->id,
    //         'link' => route('blogDetail', ($name == 'comment' ? $obj->blog->id : $obj->id)),
    //         'key_word' => $name . '-' . $obj->id
    //     ]);
    //     event(new HasNewNotificationEvent($notification));
    // }
    // public function takeEmoji($name, $obj)
    // {
    //     if ($name == 'blog') {
    //         return $this->blogRepository->takeEmoji($obj);
    //     } else if ($name == 'comment') {
    //         return $this->commentRepository->takeEmoji($obj);
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

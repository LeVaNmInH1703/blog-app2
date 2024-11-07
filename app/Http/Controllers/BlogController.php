<?php

namespace App\Http\Controllers;

use App\Events\BlogEvent;
use App\Events\HasNewNotificationEvent;
use App\Events\PostEvent;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Feeling;
use App\Models\FeelingBlogDetail;
use App\Models\FeelingCommentDetail;
use App\Models\FileBlog;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\BlogRepository;
use App\Repositories\BlogRepositoryInterface;
use App\View\Components\CountFeelingComponent;
use App\View\Components\GetFeelingComponent;
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
    public $MESSAGECONTROLLER;
    public BlogRepository $blogRepository;
    public function __construct(MessageController $messageController)
    {
        $this->MESSAGECONTROLLER = $messageController;
        $this->blogRepository = app(BlogRepositoryInterface::class);
    }
    public function index()
    {
        if (!Auth::user()->email_verified_at)
            return redirect()->route('waitToVerifyEmail');
        // dd(Auth::user()->temp);
        $blogs = $this->takeAndSetSomeBlog();
        $newBlog = session('newBlog');
        if ($newBlog) {
            $blogs = $this->takeAndSetSomeBlog(5, [$newBlog->getId()]);
            $blogs->prepend($newBlog);
            session()->forget('newBlog');
        }
        return view("pages.home", compact('blogs'));
    }
    public function createBlogGetView(){
        return view('pages.createPost');
    }
    public function loadMorePost(Request $request)
    {
        $loadedBlogs = $request->input('loadedBlogs', []); //k cho load lại các blog đã render để tránh k listen đc event bên cli
        $blogs = $this->takeAndSetSomeBlog(5, $loadedBlogs);
        $result = '';
        foreach ($blogs as $blog)
            $result .= view('components/blog-component', compact('blog'))->render();
        return $result;
    }
    public function takeAndSetSomeBlog($n = 5, $loadedBlogs = [])
    {
        // dd(Blog->temp());
        $blogs = Blog::with(['files', 'feelings', 'user', 'comments'])
            ->where('scheduled_at','<=',now())
            ->whereNotIn('id', $loadedBlogs)
            ->inRandomOrder()
            // ->skip($offset)
            ->take($n)
            ->get();
        // dd(Blog::with('images','comments','feelings')->count(),Blog::all()->count());
        foreach ($blogs as $blog)
            $this->setBlog($blog);
        return $blogs;
    }
    public function setBlog(Blog &$blog)
    {
        $blog->countFeeling = $blog->feelings->count();
        $blog->countComment = $blog->comments->count();
        //cảm xúc của người dùng với blog này
        $blog->clientFeeling = FeelingBlogDetail::where([['user_id', Auth::id()], ['blog_id', $blog->id]])->first();
        // dd($blog->clientFeeling);
    }
    public function blogDetail($blog)
    {
        $blog = Blog::with(['files', 'comments.comments', 'comments.image', 'comments.user', 'comments.feelings', 'comments.replyCommentDetail'])->withCount(['feelings', 'comments'])->where('id', $blog)->first();
        if (!$blog) abort(404);
        $this->setBlog($blog);
        $sortedComments = $blog->comments->sortByDesc('created_at');
        $blog->comments = $sortedComments;
        foreach ($blog->comments as $comment)
            if ($comment->replyCommentDetail == null) //comment này có rep comment nào k
                $this->setComment($comment); //k

        return view('pages.blogDetail', compact('blog'));
    }
    public function setComment(Comment &$comment, $level = 0)
    {
        $comment->countFeeling = $comment->feelings->count();
        $comment->clientFeeling = FeelingCommentDetail::where([['user_id', Auth::id()], ['comment_id', $comment->id]])->first();
        if ($comment->clientFeeling)
            $comment->clientFeeling = $comment->clientFeeling->feeling;
        // Log::info($comment->id.' '.$level);
        foreach ($comment->comments as $commentChild) {
            $this->setComment($commentChild);
        }
    }
    public function findObjByName($name, $objID)
    {
        if ($name == 'blog') {
            return Blog::with(['files', 'comments.comments', 'feelings'])->find($objID);
        } else if ($name == 'comment') {
            return Comment::with(['comments', 'feelings'])->find($objID);
        }
    }
    public function toggleFeeling($name, $objID)
    {
        $obj = $this->findObjByName($name, $objID);
        if (!$obj || ($name != 'blog' && $name != 'comment'))
            abort(404);
        $getFeelingRender = '';
        if ($this->takeFeeling($name, $obj)->exists()) {
            $this->useTransaction(function () use ($name, $obj) {
                $this->takeFeeling($name, $obj)->delete();
            });
            $getFeelingRender = $this->getFeeling();
        } else {
            $feeling = Feeling::where('name', 'heart')->first();
            $this->createFeeling($name, $obj, $feeling);
            $getFeelingRender = $this->getFeeling($feeling);
        }
        return response()->json([
            'countFeelingRender' => str($this->countFeeling($name, $objID)),
            'getFeelingRender' => str($getFeelingRender),
            'test' => 'aaaaaaaaaaaaaa'
        ]);
    }
    public function countFeeling($name, $objID)
    {
        $obj = $this->findObjByName($name, $objID);
        if (!$obj || ($name != 'blog' && $name != 'comment')) abort(404);
        if ($name == 'blog') {
            $obj->clientFeeling = FeelingBlogDetail::where([['user_id', Auth::id()], ['blog_id', $obj->id]])->first();
            $size = 18;
        } else if ($name == 'comment') {
            $obj->clientFeeling = FeelingCommentDetail::where([['user_id', Auth::id()], ['comment_id', $obj->id]])->first();
            $size = 12;
        }
        $obj->countFeeling = $obj->feelings->count();
        return (new CountFeelingComponent($obj, $size))->render();
    }
    public function getFeeling(Feeling $feeling = null, $isshowName = true, $size = 18)
    {
        if (($isshowName != true && $isshowName != false)) abort(404);
        return (new GetFeelingComponent($feeling, $isshowName, $size))->render();
    }
    public function makeFeeling($name, $objID, Feeling $feeling)
    {
        $obj = $this->findObjByName($name, $objID);
        if (!$obj || ($name != 'blog' && $name != 'comment') || !$feeling) {
            //blog k có hoặc người dùng bị chặn
            abort(404);
        }

        if ($this->takeFeeling($name, $obj)->exists()) {
            //update feeling
            $this->updateFeeling($name, $obj, $feeling);
        } else {
            $this->createFeeling($name, $obj, $feeling);
            //create new feeling
        }
        return response()->json([
            'countFeelingRender' => str($this->countFeeling($name, $objID)),
            'getFeelingRender' => str($this->getFeeling($feeling)),
        ]);
    }
    public function deleteFeeling($name, $objID)
    {
        $obj = $this->findObjByName($name, $objID);
        if (!$obj || ($name != 'blog' && $name != 'comment')) {
            abort(404);
        }
        $this->useTransaction(function () use ($name, $obj) {
            $this->takeFeeling($name, $obj)->first()->delete();
        });
        return redirect()->back();
    }
    public function updateFeeling($name, $obj, Feeling $feeling)
    {
        return $this->useTransaction(function () use ($name, $obj, $feeling) {
            $this->takeFeeling($name, $obj)->first()->update([
                'feeling_id' => $feeling->id
            ]);
        });
    }
    public function createFeeling($name, $obj, Feeling $feeling)
    {
        $this->createNotify($name, $obj, $feeling);
        return $this->useTransaction(function () use ($name, $obj, $feeling) {
            if ($name == 'blog') {
                FeelingBlogDetail::create([
                    'user_id' => Auth::id(),
                    'blog_id' => $obj->id,
                    'feeling_id' => $feeling->id
                ]);
            } else if ($name == 'comment') {
                FeelingCommentDetail::create([
                    'user_id' => Auth::id(),
                    'comment_id' => $obj->id,
                    'feeling_id' => $feeling->id
                ]);
            }
        });
    }
    public function createNotify($name, $obj, $feeling)
    {
        if($obj->user->id==Auth::id()) return;
        if ($obj->feelings->count() <= 1)
            $content = __("public.Someone reacted to your something", ['Someone' => Auth::user()->name, 'something' => $name]);
        else
            $content = __("public.Someone and some others reacted to your something", ['Someone' => Auth::user()->name, 'some' => $obj->feelings->count() - 1, 'something' => $name]);
        $content = $content . " " . $this->getFeeling($feeling, false, 15);
        $notification = Notification::create([
            'user_id_send' => Auth::id(),
            'content' => $content,
            'user_id_receive' => $obj->user->id,
            'link' => route('blogDetail', ($name == 'comment' ? $obj->blog->id : $obj->id)),
            'key_word'=>$name.'-'.$obj->id
        ]);
        event(new HasNewNotificationEvent($notification));
    }
    public function takeFeeling($name, $obj)
    {
        if ($name == 'blog') {
            return FeelingBlogDetail::where([['blog_id', $obj->id], ['user_id', Auth::id()]]);
        } else if ($name == 'comment') {
            return FeelingCommentDetail::where([['comment_id', $obj->id], ['user_id', Auth::id()]]);
        }
    }
    public function createBlog(Request $request)
    {
        $privacy=$request->input('privacy');
        $datetime=$request->input('datetime');
        if ($request->input('content') == "" && $request->files == "")
            return redirect()->back()->with(['message' => __('public.Please write something !')]);
        $request->validate([
            'files.*' => 'file',
        ]);
        $newBlog = new Blog();
        $result = $this->useTransaction(function () use ($request, &$newBlog) {
            $newBlog = Blog::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'scheduled_at'=>$datetime??now()
            ]);
            $files = $request->file('files');
            if ($files) {
                foreach ($files as $file) {
                    $mimeType = $file->getMimeType();
                    if ($file) {

                        if (str_starts_with($mimeType, 'image/')) {
                            $fileBlogName = 'blog_image_' . $newBlog->id . Str::random(20) . '.' . $file->extension();

                            // resize chú ý dùng đúng driver
                            $manager = new ImageManager(new Driver());
                            // $manager->read($image)->resize(250, 250)->save(public_path('images_resize') .'/'. $fileBlogName);
                            $temp = $manager->read($file);
                            if ($temp->width() > 640)
                                $temp->resize(640, 640 * $temp->height() / $temp->width());

                            $temp->save(public_path('images_resize') . '/' . $fileBlogName);

                            //move
                            $file->move(public_path('images'), $fileBlogName);
                            FileBlog::create([
                                'file_name' => $fileBlogName,
                                'blog_id' => $newBlog->id
                            ]);
                        } else if (str_starts_with($mimeType, 'video/')) {
                            $fileBlogName = 'blog_video_' . $newBlog->id . Str::random(20) . '.' . $file->extension();
                            //move
                            $file->move(public_path('videos'), $fileBlogName);

                            FileBlog::create([
                                'file_name' => $fileBlogName,
                                'blog_id' => $newBlog->id
                            ]);
                        }
                    }
                }
            }
        });
        if ($result) {
            return redirect()->route('home')->with('message', __('public.Posted'))->with('newBlog', $newBlog);
        } else {
            return redirect()->back()->with('message', "Error");
        }
    }
}

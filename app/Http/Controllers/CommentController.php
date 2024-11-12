<?php

namespace App\Http\Controllers;

use App\Events\HasNewNotificationEvent;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\ImageComment;
use App\Models\ImageCommentDetail;
use App\Models\Notification;
use App\Models\ReplyCommentDetail;
use App\View\Components\CommentComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CommentController extends Controller
{

    public function saveCommentImage(Request $request, Comment $newComment)
    {
        $image = $request->file('fileImage');
        if ($image) {
            $imageAvatarName = 'comment_image_' . $newComment->id . Str::random(20) . '.' . $image->extension();

            // resize chú ý dùng đúng driver
            $manager = new ImageManager(new Driver());
            $manager->read($image)->resize(250, 250)->save(public_path('images_resize') . '/' . $imageAvatarName);

            //move
            $image->move(public_path('images'), $imageAvatarName);
            ImageCommentDetail::create([
                'image_comment_name' => $imageAvatarName,
                'comment_id' => $newComment->id
            ]);
        }
    }
    public function createComment(Request $request, Blog $blog, Comment $comment = null)
    {
        if (!$blog)
            abort(404);
        $request->validate([
            'content' => ['required'],
            'fileImage' => ['image', 'mimes:jpeg,png,jpg,gif,svg'],
        ]);
        $newComment = new Comment();

        $this->useTransaction(function () use ($request, $blog, $comment, &$newComment) {
            $newComment = Comment::create([
                'content' => $request->input('content'),
                'blog_id' => $blog->id,
                'user_id' => Auth::id(),
            ]);
            $this->saveCommentImage($request, $newComment);
            if (!$comment) return;
            $newRepCommentDetail = ReplyCommentDetail::create([
                'comment_id' => $comment->id,
                'reply_comment_id' => $newComment->id
            ]);
        });
        if ($newComment->id) {
            $result = [
                'status' => 200,
                'commentRender' => str((new CommentComponent($newComment))->render()),
                'blogCountCommentRender' => str(view('components.blog-count-comment-component', ['number' => $blog->comments()->count()])->render()),
            ];
            if ($comment) {
                $result = array_merge($result, [
                    'commentCountCommentRender' => str(view('components.comment-count-comment-component', ['number' => $comment->comments()->count()])->render()),
                ]);
            }
            $this->createNotify($newComment,($comment?$comment->user->id:$blog->user->id), __((!$comment ? "public.Someone commented on your blog: content" : 'public.Someone replied to your comment: content'), ['Someone' => Auth::user()->name, 'content' => $newComment->content]));
            return response()->json($result);
        }

        return response()->json([
            'status' => 500,
            'error' => 'Create fail'
        ]);
    }
    public function createNotify($newComment,$user_id_receive, $content)
    {
        if($user_id_receive==Auth::id()) return;
        $notification = Notification::create([
            'user_id_send' => Auth::id(),
            'content' => $content,
            'user_id_receive' => $user_id_receive,
            'link' => route('blogDetail', $newComment->blog->id),
            'key_word'=>'comment-'.$newComment->id
        ]);
        event(new HasNewNotificationEvent($notification));
    }
}

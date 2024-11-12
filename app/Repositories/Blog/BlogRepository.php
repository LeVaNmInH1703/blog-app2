<?php

namespace App\Repositories\Blog;

use App\Models\Blog;
use App\Models\FeedbackBlogDetail;
use App\Repositories\BaseRepository;
use App\Repositories\Blog\BlogRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Blog::class);
    }
    public function getAndSetSomeBlog($n = 5, $loadedBlogs = [])
    {
        $blogs = Blog::with(['images','videos', 'feedbacks', 'user', 'comments'])
            ->where('scheduled_at', '<=', now())
            ->whereNotIn('id', $loadedBlogs)
            ->inRandomOrder()
            // ->skip($offset)
            ->take($n)
            ->get();
        foreach ($blogs as $blog)
            $this->setBlog($blog);
        return $blogs;
    }
    public function setBlog(Blog &$blog)
    {
        $blog->countFeedback = $blog->feedbacks->count();
        $blog->countComment = $blog->comments->count();
        //cảm xúc của người dùng với blog này
        $blog->clientFeedback = FeedbackBlogDetail::where([['user_id', Auth::id()], ['blog_id', $blog->id]])->first();
        // dd($blog->clientFeedback);
    }
    // public function get($blog)
    // {
    //     return Blog::with([
    //         'files',
    //         'comments.comments',
    //         'comments.image',
    //         'comments.user',
    //         'comments.feedbacks',
    //         'comments.replyCommentDetail'
    //     ])
    //         ->withCount(['feedbacks', 'comments'])
    //         ->where('id', $blog)->first();
    // }
    // public function find($id)
    // {
    //     return Blog::with(['files', 'comments.comments', 'feedbacks'])->find($id);
    // }
    // public function takeFeedback($blog)
    // {
    //     return FeedbackBlogDetail::where([['blog_id', $blog->id], ['user_id', Auth::id()]]);
    // }
    // public function deleteFeedback($blog)
    // {
    //     $this->takeFeedback($blog)->first()->delete();
    // }
    // public function updateFeedback($blog, Feedback $feedback)
    // {
    //     $this->takeFeedback($blog)->first()->update([
    //         'feedback_id' => $feedback->id
    //     ]);
    // }
}

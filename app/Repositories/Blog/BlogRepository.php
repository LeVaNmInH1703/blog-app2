<?php

namespace App\Repositories\Blog;

use App\Models\Blog;
use App\Models\EmojiBlogDetail;
use App\Repositories\BaseRepository;
use App\Repositories\Blog\BlogRepositoryInterface;
use App\Repositories\Comment\CommentRepository;
use Illuminate\Support\Facades\Auth;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public $commentRepository;
    public function __construct(CommentRepository $commentRepository)
    {
        parent::__construct(Blog::class);
        $this->commentRepository=$commentRepository;
    }
    public function getAndSetSome($n = 5, $loadedBlogs = [])
    {
        $blogs = Blog::with(['images','videos', 'emojiDetails', 'user'])
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
        $blog->countEmoji = $blog->emojiDetails->count();
        $blog->comment=$this->commentRepository->getAndSetSomeCommentBelongToBlog($blog);
        $blog->countComment = $blog->comments->count();

        //cảm xúc của người dùng với blog này
        $blog->myEmoji = EmojiBlogDetail::where([['user_id', Auth::id()], ['blog_id', $blog->id]])->first();
    }
    
}

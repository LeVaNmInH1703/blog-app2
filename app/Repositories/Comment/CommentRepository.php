<?php

namespace App\Repositories\Comment;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Emoji;
use App\Models\EmojiCommentDetail;
use App\Repositories\BaseRepository;
use App\Repositories\Comment\CommentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{

    public function __construct()
    {
        parent::__construct(Comment::class);
    }
    public function setComment(Comment &$comment)
    {
        $comment->countEmoji = $comment->emojis->count();
        $comment->myEmoji = EmojiCommentDetail::where([['user_id', Auth::id()], ['comment_id', $comment->id]])->first();

        if ($comment->myEmoji)
            $comment->myEmoji = $comment->myEmoji->emoji;
        // Log::info($comment->id.' '.$level);
        foreach ($comment->comments as $commentChild) {
            $this->setComment($commentChild);
        }
    }
    public function getAndSetSomeCommentBelongToBlog(Blog $blog, $n = 5, $loadedComments = [])
    {
        $comments = Comment::with(['images', 'videos', 'emojiDetails', 'user'])
            ->whereNotIn('id', $loadedComments)
            ->inRandomOrder()
            ->take($n)
            ->get();
        foreach ($comments as $comment)
            $this->setComment($comment);
        return $comments;
    }
}

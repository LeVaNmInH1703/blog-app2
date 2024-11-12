<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use App\Models\Feedback;
use App\Models\FeedbackCommentDetail;
use App\Repositories\BaseRepository;
use App\Repositories\Comment\CommentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Comment::class);
    }
    public function setComment(Comment &$comment, $level = 0)
    {
        $comment->countFeedback = $comment->feedbacks->count();
        $comment->clientFeedback = FeedbackCommentDetail::where([['user_id', Auth::id()], ['comment_id', $comment->id]])->first();
        if ($comment->clientFeedback)
            $comment->clientFeedback = $comment->clientFeedback->feedback;
        // Log::info($comment->id.' '.$level);
        foreach ($comment->comments as $commentChild) {
            $this->setComment($commentChild);
        }
    }
    public function find($id){
        return Comment::with(['comments', 'feedbacks'])->find($id);
    }
    public function takeFeedback($comment){
        return FeedbackCommentDetail::where([['comment_id', $comment->id], ['user_id', Auth::id()]]);
    }
    public function deleteFeedback($comment){
        $this->takeFeedback($comment)->first()->delete();
    }
    public function updateFeedback($comment, Feedback $feedback)
    {
        $this->takeFeedback($comment)->first()->update([
            'feedback_id' => $feedback->id
        ]);
    }
}

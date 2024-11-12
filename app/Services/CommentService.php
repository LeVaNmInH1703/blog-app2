<?php
namespace App\Services;

use App\Repositories\Comment\CommentRepository;

class CommentService extends BaseService{
    public function __construct(CommentRepository $commentRepository)
    {
        parent::__construct($commentRepository);
    }
    
}
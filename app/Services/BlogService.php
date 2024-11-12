<?php
namespace App\Services;

use App\Repositories\Blog\BlogRepository;

class BlogService extends BaseService {
    protected $repository;
    public function __construct(BlogRepository $blogRepository)
    {
        parent::__construct($blogRepository);
        $this->repository=$blogRepository;
    }
    public function getAndSetSomeBlog(){
        return $this->repository->getAndSetSome();
    }
}
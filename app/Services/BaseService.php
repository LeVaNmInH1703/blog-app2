<?php
namespace App\Services;

use App\Repositories\BaseRepository;

class BaseService implements BaseServiceInterface{
    protected $repository;
    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }
}
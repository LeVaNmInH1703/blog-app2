<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    /** @var Model */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginate($itemOnPage)
    {
        return $this->model->paginate($itemOnPage);
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findMany(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    public function destroy($id)
    {
        $model = $this->model->find($id);
        return $model->delete();
    }

    public function delete(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }
}
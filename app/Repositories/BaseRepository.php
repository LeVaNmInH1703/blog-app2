<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function paginate($itemOnPage)
    {
        return $this->model::paginate($itemOnPage);
    }

    public function all()
    {
        return $this->model::all();
    }

    public function find($id)
    {
        return $this->model::find($id);
    }

    public function delete($id)
    {
        $item = $this->find($id);
        if ($item) {
            return $item->delete();
        }
        return null;
    }

    public function create($data)
    {
        return $this->model::create($data);
    }

    public function update($id, $data = [])
    {
        $item = $this->find($id);
        if ($item) {
            $item->update($data);
            return $item;
        }
        return null;
    }
}
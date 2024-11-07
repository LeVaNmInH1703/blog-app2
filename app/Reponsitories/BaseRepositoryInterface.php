<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function paginate($itemOnPage);
    public function all();
    public function find($id);
    public function delete($id);
    public function create($data);
    public function update($id,$data=[]);
}

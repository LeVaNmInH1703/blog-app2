<?php

namespace App\Repositories;

use App\Models\Blog; // Giả sử bạn có một model Blog
use Illuminate\Database\Eloquent\Model;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function __construct(Blog $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters($itemOnPage, array $filters = [])
    {
        $query = $this->model->query();

        // Áp dụng các bộ lọc, ví dụ như theo tiêu đề hoặc trạng thái
        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($itemOnPage);
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $blog = $this->find($id);
        if ($blog) {
            $blog->update($data);
            return $blog;
        }
        return null; // Hoặc xử lý lỗi nếu bài viết không tồn tại
    }
}
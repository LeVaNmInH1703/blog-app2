<?php

namespace App\Repositories;

interface BlogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Lấy danh sách bài viết được phân trang, với các điều kiện lọc
     *
     * @param int $itemOnPage
     * @param array $filters
     * @return mixed
     */
    public function paginateWithFilters($itemOnPage, array $filters = []);

    /**
     * Tìm một bài viết dựa trên slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug($slug);

    /**
     * Tạo một bài viết mới
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Cập nhật một bài viết
     *
     * @param int|string $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);
}
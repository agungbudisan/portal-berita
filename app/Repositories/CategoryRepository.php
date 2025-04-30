<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    /**
     * Get all categories with news count
     *
     * @param int $limit
     * @return Collection
     */
    public function getAllWithNewsCount(int $limit = 0): Collection
    {
        $query = Category::withCount('news')
            ->orderBy('news_count', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get categories for admin with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Category::withCount('news')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get categories except the one specified
     *
     * @param Category $except
     * @param int $limit
     * @return Collection
     */
    public function getExcept(Category $except, int $limit = 4): Collection
    {
        return Category::where('id', '!=', $except->id)
            ->withCount('news')
            ->orderBy('news_count', 'desc')
            ->limit($limit)
            ->get();
    }
}

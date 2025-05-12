<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Get all categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Category::orderBy('name')->get();
    }

    /**
     * Get all categories with news count.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithNewsCount()
    {
        return Category::withCount('news')->orderBy('name')->get();
    }

    /**
     * Get trending categories (categories with most news).
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrending($limit = 6)
    {
        return Category::withCount('news')
            ->orderBy('news_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get categories except the given one.
     *
     * @param Category $category
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExcept(Category $category, $limit = 6)
    {
        return Category::withCount('news')
            ->where('id', '!=', $category->id)
            ->orderBy('news_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get categories by initial letter.
     *
     * @param string $letter
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByInitialLetter($letter)
    {
        return Category::where('name', 'LIKE', $letter . '%')
            ->withCount('news')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories with news count and limit.
     *
     * @param int $excludeCategoryId
     * @param int $limit
     * @return Collection
     */
    public function getOtherCategoriesWithCount(int $excludeCategoryId, int $limit = 6): Collection
    {
        return Category::withCount(['news' => function ($query) {
                $query->where('status', 'published')
                      ->whereNotNull('published_at');
            }])
            ->where('id', '!=', $excludeCategoryId)
            ->having('news_count', '>', 0)
            ->orderBy('news_count', 'desc')
            ->limit($limit)
            ->get();
    }
}

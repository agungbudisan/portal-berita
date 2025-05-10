<?php

namespace App\Repositories;

use App\Models\Category;

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
}

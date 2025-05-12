<?php

namespace App\Repositories;

use App\Models\News;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NewsRepository
{
    /**
     * Get the latest news with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLatestNews(int $perPage = 8): LengthAwarePaginator
    {
        return News::with('category')
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get featured news (typically the latest one)
     *
     * @return News|null
     */
    public function getFeaturedNews(): ?News
    {
        return News::with('category')
            ->latest('published_at')
            ->first();
    }

    /**
     * Get popular news based on comment count
     *
     * @param int $limit
     * @return Collection
     */
    public function getPopularNews(int $limit = 5): Collection
    {
        return News::with('category')
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get news by category with pagination
     *
     * @param Category $category
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getNewsByCategory(Category $category, int $perPage = 10): LengthAwarePaginator
    {
        return $category->news()
            ->with('category')
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get popular news within a specific category
     *
     * @param Category $category
     * @param int $limit
     * @return Collection
     */
    public function getPopularByCategory(int $categoryId, int $limit = 5): Collection
    {
        return News::where('category_id', $categoryId)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get related news based on the same category
     *
     * @param News $news
     * @param int $limit
     * @return Collection
     */
    public function getRelatedNews(News $news, int $limit = 2): Collection
    {
        return News::where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->with('category')
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search news by query
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchNews(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return News::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->with('category')
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get news count for today
     *
     * @return int
     */
    public function getTodayNewsCount(): int
    {
        return News::whereDate('created_at', today())->count();
    }
}

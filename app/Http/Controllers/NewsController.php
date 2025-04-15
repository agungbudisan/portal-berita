<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\SavedNews;
use App\Services\NewsApiService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $newsApiService;

    public function __construct(NewsApiService $newsApiService)
    {
        $this->newsApiService = $newsApiService;
    }

    public function index()
    {
        $headlines = $this->newsApiService->getTopHeadlines();
        $categories = Category::where('is_active', true)->orderBy('display_order')->get();

        // Get the latest published articles
        $localArticles = Article::where('is_published', true)
        ->whereNotNull('published_at')
        ->with('category', 'author')
        ->orderByDesc('published_at')
        ->limit(6)
        ->get();

        return view('news.index', [
            'headlines' => $headlines['articles'] ?? [],
            'categories' => $categories,
            'localArticles' => $localArticles,
        ]);
    }

    public function show($source, $id)
    {
        // Untuk berita dari API, kita akan mencari berdasarkan ID atau URL
        // Implementasi ini bergantung pada API yang digunakan

        $newsData = $this->newsApiService->searchNews($id);
        $article = $newsData['articles'][0] ?? null;

        if (!$article) {
            abort(404);
        }

        $comments = Comment::where('news_api_id', $id)
            ->where('is_approved', true)
            ->with('user')
            ->get();

        return view('news.show', [
            'article' => $article,
            'comments' => $comments
        ]);
    }

    public function byCategory(Category $category, Request $request)
    {
        $page = $request->input('page', 1);
        $pageSize = 12;
        $newsData = $this->newsApiService->getNewsByCategory(strtolower($category->name), $page, $pageSize);

        // Mendapatkan artikel lokal dengan kategori yang sama
        $localArticles = Article::where('is_published', true)
            ->where('category_id', $category->id)
            ->whereNotNull('published_at')
            ->with('category', 'author')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        return view('news.category', [
            'category' => $category,
            'news' => $newsData['articles'] ?? [],
            'totalResults' => $newsData['totalResults'] ?? 0,
            'page' => $page,
            'pageSize' => $pageSize,
            'localArticles' => $localArticles
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);

        if (empty($query)) {
            return redirect()->route('news.index');
        }

        $results = $this->newsApiService->searchNews($query, $page);

        return view('news.search', [
            'query' => $query,
            'results' => $results['articles'] ?? [],
            'page' => $page,
            'totalResults' => $results['totalResults'] ?? 0
        ]);
    }
}

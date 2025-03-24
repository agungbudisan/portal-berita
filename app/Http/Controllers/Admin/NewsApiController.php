<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NewsApiService;
use App\Models\Category;
use App\Models\SavedArticle;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewsApiController extends Controller
{
    protected $newsApiService;

    public function __construct(NewsApiService $newsApiService)
    {
        $this->newsApiService = $newsApiService;
    }

    public function index(Request $request)
    {
        $category = $request->input('category');
        $query = $request->input('q');
        $params = [];

        if ($category) {
            $params['category'] = $category;
        }

        $apiArticles = [];
        $error = null;

        if ($query) {
            $apiResponse = $this->newsApiService->searchNews(['q' => $query]);
            if (isset($apiResponse['error'])) {
                $error = $apiResponse['error'];
            } elseif (isset($apiResponse['articles'])) {
                $apiArticles = $apiResponse['articles'];
            }
        } elseif ($request->has('fetch')) {
            $apiResponse = $this->newsApiService->getHeadlines($params);
            if (isset($apiResponse['error'])) {
                $error = $apiResponse['error'];
            } elseif (isset($apiResponse['articles'])) {
                $apiArticles = $apiResponse['articles'];
            }
        }

        $categories = Category::active()->ordered()->get();

        return Inertia::render('Admin/NewsApi', [
            'apiArticles' => $apiArticles,
            'categories' => $categories,
            'filters' => $request->only(['category', 'q']),
            'error' => $error
        ]);
    }

    public function saveArticle(Request $request)
    {
        $request->validate([
            'article_id' => 'required',
            'title' => 'required',
            'url' => 'required|url',
            'category_id' => 'required|exists:categories,id'
        ]);

        $article = SavedArticle::updateOrCreate(
            ['article_id' => $request->article_id],
            [
                'title' => $request->title,
                'description' => $request->description,
                'url' => $request->url,
                'url_to_image' => $request->url_to_image,
                'source_name' => $request->source_name,
                'published_at' => $request->published_at ?: now(),
                'content' => $request->content,
                'category_id' => $request->category_id,
                'user_id' => $request->user()->id,
                'is_published' => $request->has('is_published'),
            ]
        );

        return redirect()->route('admin.saved-articles.index')
            ->with('success', 'Artikel berhasil disimpan' . ($request->has('is_published') ? ' dan dipublikasikan' : ''));
    }
}

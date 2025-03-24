<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedArticle;
use App\Models\Category;
use App\Models\ArticleView;
use App\Models\UserReadingHistory;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = SavedArticle::with('category')
                   ->where('is_published', true);

        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest('published_at')->paginate(12);

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search', 'category'])
        ]);
    }

    public function show(Request $request, $id)
    {
        $article = SavedArticle::with(['category', 'comments' => function($query) {
            $query->where('is_approved', true)
                  ->whereNull('parent_id')
                  ->with(['user', 'replies' => function($q) {
                      $q->where('is_approved', true)->with('user');
                  }]);
        }])->where('is_published', true)->findOrFail($id);

        // Increment view count
        $article->increment('view_count');

        // Record view
        ArticleView::create([
            'saved_article_id' => $article->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user()->id,
            'viewed_at' => now(),
        ]);

        // Record reading history for logged in users
        if ($request->user()) {
            UserReadingHistory::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'saved_article_id' => $article->id,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        $related_articles = SavedArticle::with('category')
                                      ->where('is_published', true)
                                      ->where('category_id', $article->category_id)
                                      ->where('id', '!=', $article->id)
                                      ->latest('published_at')
                                      ->take(3)
                                      ->get();

        // Check if article is bookmarked by current user
        $isBookmarked = false;
        if ($request->user()) {
            $isBookmarked = $request->user()->bookmarks()
                                       ->where('saved_article_id', $article->id)
                                       ->exists();
        }

        return Inertia::render('Articles/Show', [
            'article' => $article,
            'related_articles' => $related_articles,
            'isBookmarked' => $isBookmarked
        ]);
    }

    public function byCategory(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $articles = SavedArticle::with('category')
                              ->where('is_published', true)
                              ->where('category_id', $category->id)
                              ->latest('published_at')
                              ->paginate(12);

        return Inertia::render('Articles/Category', [
            'articles' => $articles,
            'category' => $category
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('q');

        $articles = SavedArticle::with('category')
                              ->where('is_published', true)
                              ->where(function($query) use ($search) {
                                   $query->where('title', 'like', "%{$search}%")
                                         ->orWhere('description', 'like', "%{$search}%")
                                         ->orWhere('content', 'like', "%{$search}%");
                              })
                              ->latest('published_at')
                              ->paginate(12);

        return Inertia::render('Articles/Search', [
            'articles' => $articles,
            'search' => $search
        ]);
    }
}

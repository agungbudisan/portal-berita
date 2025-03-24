<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavedArticle;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'articles' => SavedArticle::count(),
            'published_articles' => SavedArticle::where('is_published', true)->count(),
            'users' => User::where('role', 'user')->count(),
            'comments' => Comment::count(),
            'pending_comments' => Comment::where('is_approved', false)->count(),
        ];

        $recent_articles = SavedArticle::with('category', 'user')
                                      ->latest()
                                      ->take(5)
                                      ->get();

        $popular_articles = SavedArticle::with('category')
                                      ->orderBy('view_count', 'desc')
                                      ->take(5)
                                      ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recent_articles' => $recent_articles,
            'popular_articles' => $popular_articles
        ]);
    }
}

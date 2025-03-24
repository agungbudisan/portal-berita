<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedArticle;
use App\Models\Category;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $featuredArticles = SavedArticle::with('category')
                                        ->where('is_published', true)
                                        ->latest('published_at')
                                        ->take(5)
                                        ->get();

        $latestArticles = SavedArticle::with('category')
                                      ->where('is_published', true)
                                      ->latest('published_at')
                                      ->skip(5)
                                      ->take(6)
                                      ->get();

        $popularArticles = SavedArticle::with('category')
                                       ->where('is_published', true)
                                       ->orderBy('view_count', 'desc')
                                       ->take(5)
                                       ->get();

        $categories = Category::where('is_active', true)
                             ->orderBy('display_order')
                             ->get();

        return Inertia::render('Home', [
            'featuredArticles' => $featuredArticles,
            'latestArticles' => $latestArticles,
            'popularArticles' => $popularArticles,
            'categories' => $categories
        ]);
    }
}

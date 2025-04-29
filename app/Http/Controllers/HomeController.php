<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredNews = News::with('category')
            ->latest('published_at')
            ->first();

        $latestNews = News::with('category')
            ->latest('published_at')
            ->take(8)
            ->get();

        $popularNews = News::with('category')
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->take(5)
            ->get();

        $categories = Category::withCount('news')
            ->orderBy('news_count', 'desc')
            ->take(5)
            ->get();

        return view('home', compact(
            'featuredNews',
            'latestNews',
            'popularNews',
            'categories'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Repositories\NewsRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $newsRepository;
    protected $categoryRepository;

    public function __construct(NewsRepository $newsRepository, CategoryRepository $categoryRepository)
    {
        $this->newsRepository = $newsRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        // Ambil berita unggulan (featured) yang sudah dipublikasikan
        $featuredNews = News::with('category')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->first();

        // Ambil berita terbaru yang sudah dipublikasikan
        $latestNews = News::with('category')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(6);

        // Ambil berita populer yang sudah dipublikasikan
        $popularNews = News::with('category')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        // Ambil kategori dengan jumlah berita (hanya menghitung berita yang published)
        $categories = Category::withCount(['news' => function ($query) {
            $query->where('status', 'published')
                ->whereNotNull('published_at');
        }])
        ->orderBy('news_count', 'desc')
        ->get();

        return view('home', compact('featuredNews', 'latestNews', 'popularNews', 'categories'));
    }
}

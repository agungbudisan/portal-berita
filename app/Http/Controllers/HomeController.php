<?php

namespace App\Http\Controllers;

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
        $featuredNews = $this->newsRepository->getFeaturedNews();
        $latestNews = $this->newsRepository->getLatestNews(8);
        $popularNews = $this->newsRepository->getPopularNews(5);
        $categories = $this->categoryRepository->getAllWithNewsCount(5);

        return view('home', compact(
            'featuredNews',
            'latestNews',
            'popularNews',
            'categories'
        ));
    }
}

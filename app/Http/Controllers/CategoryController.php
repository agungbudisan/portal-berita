<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\NewsRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
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
        $categories = $this->categoryRepository->getAllWithNewsCount();

        // Group categories by first letter for alphabetical index
        $categoriesByLetter = [];
        foreach ($categories as $category) {
            $firstLetter = strtoupper(substr($category->name, 0, 1));
            if (!isset($categoriesByLetter[$firstLetter])) {
                $categoriesByLetter[$firstLetter] = [];
            }
            $categoriesByLetter[$firstLetter][] = $category;
        }

        // Get trending categories (top categories by news count)
        $trendingCategories = $this->categoryRepository->getTrending(8);

        return view('category.index', compact(
            'categories',
            'categoriesByLetter',
            'trendingCategories'
        ));
    }
    
    public function show(Category $category)
    {
        $news = $this->newsRepository->getNewsByCategory($category, 10);
        $popularInCategory = $this->newsRepository->getPopularNewsByCategory($category, 3);
        $otherCategories = $this->categoryRepository->getExcept($category, 4);

        return view('category.show', compact(
            'category',
            'news',
            'popularInCategory',
            'otherCategories'
        ));
    }
}

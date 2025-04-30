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

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

    // Konstanta untuk konfigurasi pagination
    private const ITEMS_PER_PAGE = 10;
    private const POPULAR_ITEMS_LIMIT = 5;
    private const OTHER_CATEGORIES_LIMIT = 6;

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

    public function show(Request $request, Category $category)
    {
        // Dapatkan berita berdasarkan kategori
        $news = $category->news()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->paginate(self::ITEMS_PER_PAGE);

        // Jika request AJAX (untuk infinite scroll), kembalikan hanya partial view
        if ($request->ajax()) {
            return view('components.news-list', compact('news'));
        }

        // Dapatkan berita populer dalam kategori (berdasarkan views atau parameter lain)
        $popularInCategory = $this->newsRepository->getPopularByCategory(
            $category->id,
            self::POPULAR_ITEMS_LIMIT
        );

        // Dapatkan kategori lainnya untuk sidebar
        $otherCategories = $this->categoryRepository->getOtherCategoriesWithCount(
            $category->id,
            self::OTHER_CATEGORIES_LIMIT
        );

        return view('category.show', compact(
            'category',
            'news',
            'popularInCategory',
            'otherCategories'
        ));
    }
}

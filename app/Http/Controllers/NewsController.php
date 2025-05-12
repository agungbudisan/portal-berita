<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        // Base query untuk berita yang sudah dipublikasikan
        $query = News::with('category')
            ->where('status', 'published')
            ->whereNotNull('published_at');

        // Filter berdasarkan kategori jika ada
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        // Pengurutan berita
        if ($request->has('sort') && !empty($request->sort)) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest('published_at');
                    break;
                case 'most_viewed':
                    $query->orderBy('views_count', 'desc');
                    break;
                case 'most_commented':
                    $query->withCount('comments')->orderBy('comments_count', 'desc');
                    break;
                default:
                    $query->latest('published_at');
            }
        } else {
            $query->latest('published_at');
        }

        // Ambil semua kategori untuk filter
        $categories = Category::withCount(['news' => function($query) {
            $query->where('status', 'published')->whereNotNull('published_at');
        }])->orderBy('name')->get();

        // Dapatkan berita dengan pagination
        $news = $query->paginate(12)->withQueryString();

        return view('news.index', compact('news', 'categories'));
    }

    public function show(News $news)
    {
        // Jika berita masih draft dan pengguna bukan admin, redirect ke halaman utama
        if ($news->status === 'draft' && (!Auth::check() || !Auth::user()==='admin')) {
            return redirect()->route('home')
                ->with('error', 'Berita tidak tersedia.');
        }

        // Increment views count
        $news->increment('views_count');

        // Load relasi yang diperlukan
        $news->load(['category', 'approvedComments.user']);

        // Ambil berita terkait dari kategori yang sama (hanya yang published)
        $relatedNews = News::where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(2)
            ->get();

        // Ambil berita populer berdasarkan jumlah komentar (hanya yang published)
        $popularNews = News::withCount('comments')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('comments_count', 'desc')
            ->take(3)
            ->get();

        // Cek status bookmark
        $isBookmarked = Auth::check() ? $news->isBookmarkedByUser(Auth::user()) : false;

        // Purifikasi konten HTML
        $purifier = $this->getHtmlPurifier();
        $purifiedContent = $purifier->purify($news->content);

        return view('news.show', compact(
            'news',
            'relatedNews',
            'popularNews',
            'isBookmarked',
            'purifiedContent'
        ));
    }

    /**
     * Mendapatkan instance HTML Purifier dengan konfigurasi yang sesuai
     *
     * @return \HTMLPurifier
     */
    private function getHtmlPurifier()
    {
        // Inisialisasi HTML Purifier dengan konfigurasi keamanan
        $config = HTMLPurifier_Config::createDefault();

        // Konfigurasi dasar
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

        // Konfigurasi cache
        $cachePath = storage_path('app/purifier');
        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cachePath);

        // Whitelist tag HTML yang diizinkan
        $config->set('HTML.Allowed', 'p,b,i,u,strong,em,a[href|title],ul,ol,li,br,span,div,h1,h2,h3,h4,h5,h6,img[src|alt|title|width|height],table[width|border],tr,td[width],th[width],thead,tbody,hr,blockquote');

        // Konfigurasi tambahan
        $config->set('HTML.TargetBlank', true);
        $config->set('AutoFormat.RemoveEmpty', false);
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,color,background-color,text-align');

        // URI config
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
        ]);

        return new HTMLPurifier($config);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $news = News::where('status', 'published')
            ->whereNotNull('published_at')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(10);

        return view('news.search', compact('news', 'query'));
    }
}

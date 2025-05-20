<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class NewsManagementController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua kategori untuk dropdown filter
        $categories = Category::all();

        // Siapkan query builder untuk news
        $query = News::with('category')->latest();

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan status (published/draft)
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal publikasi
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        // Filter berdasarkan jumlah tampilan
        if ($request->has('views_min') && is_numeric($request->views_min)) {
            $query->where('views_count', '>=', $request->views_min);
        }

        if ($request->has('views_max') && is_numeric($request->views_max)) {
            $query->where('views_count', '<=', $request->views_max);
        }

        // Filter berdasarkan sumber
        if ($request->has('source') && !empty($request->source)) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }

        // Sortir berdasarkan pilihan user
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'most_viewed':
                    $query->orderBy('views_count', 'desc');
                    break;
                case 'most_commented':
                    $query->withCount('comments')->orderBy('comments_count', 'desc');
                    break;
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                default: // 'newest' is default
                    $query->latest();
            }
        }

        // Eksekusi query dengan paginasi
        $news = $query->paginate(10)->withQueryString();

        return view('admin.news.index', compact('news', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'source' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:1024',
            'status' => 'nullable|in:published,draft',
        ]);

        $slug = Str::slug($validated['title']);
        $uniqueSlug = $this->getUniqueSlug($slug);

        $newsData = [
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'source' => $validated['source'] ?? 'WinniNews',
            'status' => $validated['status'] ?? 'published',
            'published_at' => ($validated['status'] ?? 'published') === 'published' ? now() : null,
        ];

        if ($request->hasFile('image')) {
            try {
                $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                    'folder' => 'winninews/news',
                    'transformation' => [
                        'width' => 800,
                        'height' => 600,
                        'crop' => 'limit'
                    ]
                ]);

                $newsData['image_url'] = $uploadedFile->getSecurePath();
                $newsData['cloudinary_public_id'] = $uploadedFile->getPublicId();
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal mengupload gambar: ' . $e->getMessage());
            }
        } elseif (!empty($validated['image_url'])) {
            // Jika ada image_url dari API
            $newsData['image_url'] = $validated['image_url'];
            $newsData['cloudinary_public_id'] = null; // Tidak ada public ID dari Cloudinary
        }

        News::create($newsData);

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        $categories = Category::all();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'source' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|in:published,draft',
            'remove_image' => 'nullable|boolean',
        ]);

        // Update slug hanya jika judul berubah
        if ($news->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $uniqueSlug = $this->getUniqueSlug($slug, $news->id);
            $news->slug = $uniqueSlug;
        }

        $news->title = $validated['title'];
        $news->content = $validated['content'];
        $news->category_id = $validated['category_id'];
        $news->source = $validated['source'] ?? $news->source;
        $news->status = $validated['status'] ?? $news->status;

        // Update published_at
        if ($news->status === 'draft' && $validated['status'] === 'published') {
            $news->published_at = now();
        } elseif ($news->status === 'published' && $validated['status'] === 'draft') {
            $news->published_at = null;
        }

        // Handle penghapusan gambar
        if ($request->has('remove_image') && $request->remove_image && $news->cloudinary_public_id) {
            try {
                Cloudinary::destroy($news->cloudinary_public_id);
                $news->image_url = null;
                $news->cloudinary_public_id = null;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus gambar: ' . $e->getMessage());
            }
        }

        // Handle upload gambar baru
        if ($request->hasFile('image')) {
            try {
                // Hapus gambar lama jika ada
                if ($news->cloudinary_public_id) {
                    Cloudinary::destroy($news->cloudinary_public_id);
                }

                $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), [
                    'folder' => 'winninews/news',
                    'transformation' => [
                        'width' => 800,
                        'height' => 600,
                        'crop' => 'limit'
                    ]
                ]);

                $news->image_url = $uploadedFile->getSecurePath();
                $news->cloudinary_public_id = $uploadedFile->getPublicId();
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal mengupload gambar: ' . $e->getMessage());
            }
        }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        try {
            // Hapus gambar dari Cloudinary jika ada
            if ($news->cloudinary_public_id) {
                Cloudinary::destroy($news->cloudinary_public_id);
            }

            $news->delete();

            return redirect()->route('admin.news.index')
                ->with('success', 'Berita berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.news.index')
                ->with('error', 'Gagal menghapus berita: ' . $e->getMessage());
        }
    }

    private function getUniqueSlug($slug, $ignoreId = null)
    {
        $originalSlug = $slug;
        $count = 1;

        $query = News::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = News::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    /**
     * Upload gambar dari editor WYSIWYG
     */
    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('file')) {
            try {
                $uploadedFile = Cloudinary::upload($request->file('file')->getRealPath(), [
                    'folder' => 'winninews/editor',
                    'transformation' => [
                        'width' => 1200,
                        'crop' => 'limit'
                    ]
                ]);

                return response()->json([
                    'url' => $uploadedFile->getSecurePath()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Gagal mengupload gambar: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'error' => 'File tidak valid'
        ], 400);
    }
}

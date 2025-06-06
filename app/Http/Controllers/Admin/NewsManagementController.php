<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

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
            // 'image' => 'nullable|image|max:2048',
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
            'image_url' => $validated['image_url'],
            'cloudinary_public_id' => null,
        ];

        // if ($request->hasFile('image')) {
        //     try {
        //         Log::info('Mulai upload gambar ke Cloudinary');

        //         $result = Cloudinary::upload(
        //             $request->file('image')->getRealPath(),
        //             ['folder' => 'news']
        //         );

        //         Log::info('Hasil upload: ' . json_encode($result));

        //         // Periksa jika hasil valid
        //         $uploaded = $result->getResult();

        //         if (!$uploaded) {
        //             throw new \Exception('Hasil upload Cloudinary null');
        //         }

        //         Log::info('Hasil getResult(): ' . json_encode($uploaded));

        //         $newsData['image_url'] = $uploaded['secure_url'] ?? null;
        //         $newsData['cloudinary_public_id'] = $uploaded['public_id'] ?? null;

        //         if (!$newsData['image_url'] || !$newsData['cloudinary_public_id']) {
        //             throw new \Exception('URL atau ID gambar kosong');
        //         }
        //     } catch (\Throwable $e) {
        //         Log::error('Error upload Cloudinary: ' . $e->getMessage());
        //         Log::error('Stack trace: ' . $e->getTraceAsString());

        //         return redirect()->back()->withInput()->withErrors([
        //             'image' => 'Gagal upload ke Cloudinary: ' . $e->getMessage()
        //         ]);
        //     }
        // }

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
            // 'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|url|max:1024',
            'status' => 'nullable|in:published,draft',
            // 'remove_image' => 'nullable|boolean',
        ]);

        // Slug update
        if ($news->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $news->slug = $this->getUniqueSlug($slug, $news->id);
        }

        $news->title = $validated['title'];
        $news->content = $validated['content'];
        $news->category_id = $validated['category_id'];
        $news->source = $validated['source'] ?? $news->source;
        $news->image_url = $validated['image_url'];
        $news->cloudinary_public_id = null;

        // Status dan published_at update
        $newStatus = $validated['status'] ?? $news->status;
        if ($news->status === 'draft' && $newStatus === 'published') {
            $news->published_at = now();
        } elseif ($news->status === 'published' && $newStatus === 'draft') {
            $news->published_at = null;
        }
        $news->status = $newStatus;

        // if ($request->hasFile('image')) {
        //     // Hapus gambar lama jika ada
        //     if ($news->cloudinary_public_id) {
        //         try {
        //             Cloudinary::destroy($news->cloudinary_public_id);
        //         } catch (\Throwable $e) {
        //             Log::warning('Gagal menghapus gambar lama: ' . $e->getMessage());
        //             // Lanjutkan proses upload meskipun gagal menghapus yang lama
        //         }
        //     }

        //     try {
        //         // Tambahkan logging untuk debug
        //         Log::info('Memulai proses upload gambar ke Cloudinary');

        //         // Generate timestamp untuk signed upload
        //         $timestamp = time();

        //         // Buat parameter signed upload
        //         $params = [
        //             'folder' => 'news',
        //             'timestamp' => $timestamp,
        //             // Anda bisa menambahkan parameter lain seperti transformation jika diperlukan
        //         ];

        //         // Gunakan signedUpload jika tersedia di versi library Anda
        //         // atau upload biasa dengan parameter timestamp
        //         $result = Cloudinary::upload(
        //             $request->file('image')->getRealPath(),
        //             $params
        //         );

        //         // Log hasil upload untuk debugging
        //         Log::info('Hasil upload raw: ' . json_encode($result));

        //         // Ambil hasil dengan handling null
        //         $uploaded = $result->getResult();

        //         // Validasi hasil upload
        //         if (!$uploaded || !isset($uploaded['secure_url']) || !isset($uploaded['public_id'])) {
        //             throw new \Exception('Hasil upload tidak valid: ' . json_encode($uploaded));
        //         }

        //         Log::info('Upload berhasil: ' . $uploaded['public_id']);

        //         // Update model dengan URL dan public_id baru
        //         $news->image_url = $uploaded['secure_url'];
        //         $news->cloudinary_public_id = $uploaded['public_id'];

        //     } catch (\Throwable $e) {
        //         Log::error('Cloudinary upload error: ' . $e->getMessage());
        //         Log::error('Stack trace: ' . $e->getTraceAsString());

        //         return redirect()->back()->withInput()->withErrors([
        //             'image' => 'Gagal mengupload gambar ke Cloudinary: ' . $e->getMessage()
        //         ]);
        //     }
        // }

        $news->save();

        return redirect()->route('admin.news.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        try {
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
                $uploaded = Cloudinary::upload(
                    $request->file('file')->getRealPath(),
                    ['folder' => 'news/content']
                )->getResult();

                return response()->json([
                    'url' => $uploaded['secure_url'] ?? null
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'error' => 'Gagal upload ke Cloudinary: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'error' => 'File tidak valid'
        ], 400);
    }
}

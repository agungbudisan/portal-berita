<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSource;
use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApiSourceController extends Controller
{
    public function index()
    {
        $apiSources = ApiSource::all();
        return view('admin.api-sources.index', compact('apiSources'));
    }

    public function create()
    {
        return view('admin.api-sources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:api_sources',
            'url' => 'required|url',
            'api_key' => 'nullable|string',
        ]);

        ApiSource::create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'api_key' => $validated['api_key'],
            'status' => 'active',
        ]);

        return redirect()->route('admin.api-sources.index')
            ->with('success', 'Sumber API berhasil ditambahkan.');
    }

    public function edit(ApiSource $apiSource)
    {
        return view('admin.api-sources.edit', compact('apiSource'));
    }

    public function update(Request $request, ApiSource $apiSource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:api_sources,name,' . $apiSource->id,
            'url' => 'required|url',
            'api_key' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $apiSource->update($validated);

        return redirect()->route('admin.api-sources.index')
            ->with('success', 'Sumber API berhasil diperbarui.');
    }

    public function destroy(ApiSource $apiSource)
    {
        $apiSource->delete();

        return redirect()->route('admin.api-sources.index')
            ->with('success', 'Sumber API berhasil dihapus.');
    }

    public function refresh(ApiSource $apiSource)
    {
        // Contoh integrasi dengan News API (newsapi.org)
        // Dalam proyek nyata, sesuaikan dengan API yang Anda gunakan
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $apiSource->api_key,
            ])->get($apiSource->url, [
                'country' => 'id', // Untuk berita Indonesia
                'pageSize' => 20,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $count = 0;

                if (!empty($data['articles'])) {
                    foreach ($data['articles'] as $article) {
                        // Skip jika tidak ada judul atau deskripsi
                        if (empty($article['title']) || empty($article['description'])) {
                            continue;
                        }

                        // Cek apakah berita sudah ada (berdasarkan title)
                        $exists = News::where('title', $article['title'])->exists();
                        if ($exists) {
                            continue;
                        }

                        // Ambil atau buat kategori
                        $categoryName = $article['source']['name'] ?? 'Umum';
                        $category = Category::firstOrCreate(
                            ['slug' => Str::slug($categoryName)],
                            ['name' => $categoryName]
                        );

                        // Buat berita baru
                        News::create([
                            'title' => $article['title'],
                            'slug' => Str::slug($article['title']),
                            'content' => $article['description'] . "\n\n" . ($article['content'] ?? ''),
                            'image' => $article['urlToImage'] ?? null,
                            'source' => $article['source']['name'] ?? $apiSource->name,
                            'api_id' => $article['url'] ?? null,
                            'category_id' => $category->id,
                            'published_at' => $article['publishedAt'] ? now()->parse($article['publishedAt']) : now(),
                        ]);

                        $count++;
                    }
                }

                // Update API source record
                $apiSource->update([
                    'last_sync' => now(),
                    'news_count' => $apiSource->news_count + $count
                ]);

                return back()->with('success', "Berhasil menambahkan {$count} berita baru.");
            }

            return back()->with('error', 'Gagal mengambil data dari API: ' . ($response->json()['message'] ?? 'Error tidak diketahui'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

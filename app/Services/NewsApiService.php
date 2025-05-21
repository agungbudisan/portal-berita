<?php

namespace App\Services;

use App\Models\ApiSource;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    /**
     * Test API connection without saving data
     *
     * @param ApiSource $apiSource
     * @return array
     */
    public function testConnection(ApiSource $apiSource): array
    {
        try {
            // Prepare API request
            $request = Http::timeout(10);

            // Process based on API type
            switch ($apiSource->name) {
                case 'News API':
                    $request = $request->withHeaders(['X-Api-Key' => $apiSource->api_key]);
                    $params = array_merge(['country' => 'us', 'pageSize' => 5], $apiSource->params ?? []);
                    break;

                case 'GNews':
                    $params = array_merge([
                        'apikey' => $apiSource->api_key,
                        'category' => 'general',
                        'lang' => 'en',
                        'country' => 'us',
                        'max' => 5,
                    ], $apiSource->params ?? []);
                    break;

                default:
                    // For custom APIs, use the params as provided
                    $params = $apiSource->params ?? [];
                    if ($apiSource->api_key) {
                        $request = $request->withHeaders(['X-Api-Key' => $apiSource->api_key]);
                    }
            }

            // Make the API request
            $response = $request->get($apiSource->url, $params);

            if ($response->successful()) {
                $data = $response->json();

                // Check if response has expected structure based on API type
                if ($apiSource->name === 'News API' || $apiSource->name === 'GNews') {
                    if (isset($data['articles']) && is_array($data['articles'])) {
                        $articleCount = count($data['articles']);
                        return [
                            'success' => true,
                            'message' => "Koneksi berhasil! API mengembalikan {$articleCount} artikel.",
                            'preview' => array_slice($data['articles'], 0, 3) // Return first 3 articles as preview
                        ];
                    }
                }

                // Generic response for other APIs or unexpected structure
                return [
                    'success' => true,
                    'message' => "Koneksi berhasil! Silakan periksa apakah struktur respons sesuai dengan yang diharapkan.",
                    'preview' => $data
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke API: ' . ($response->json()['message'] ?? $response->status())
            ];
        } catch (Exception $e) {
            Log::error('API Test Connection error: ' . $e->getMessage(), [
                'api_name' => $apiSource->name,
                'url' => $apiSource->url
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fetch news from a specific API source
     *
     * @param ApiSource $apiSource
     * @return array
     */
    public function fetchFromApi(ApiSource $apiSource): array
    {
        try {
            // Skip if API is inactive
            if ($apiSource->status !== 'active') {
                return [
                    'success' => false,
                    'message' => 'API is currently inactive.',
                    'count' => 0
                ];
            }

            // Make the request to the API with correct parameters based on API source
            $response = null;

            switch ($apiSource->name) {
                case 'News API':
                    $params = array_merge([
                        'country' => 'us', // US news
                        'pageSize' => 20,
                    ], $apiSource->params ?? []);

                    $response = Http::withHeaders([
                        'X-Api-Key' => $apiSource->api_key,
                    ])->get($apiSource->url, $params);
                    break;

                case 'GNews':
                    $params = array_merge([
                        'apikey' => $apiSource->api_key,
                        'category' => 'general',
                        'lang' => 'en', // English language
                        'country' => 'us', // US
                        'max' => 10, // Limit to 10 news
                    ], $apiSource->params ?? []);

                    $response = Http::get($apiSource->url, $params);
                    break;

                default:
                    // For other API sources, try a generic approach with merged params
                    $params = $apiSource->params ?? [];
                    $request = Http::timeout(30);

                    if ($apiSource->api_key) {
                        $request = $request->withHeaders(['X-Api-Key' => $apiSource->api_key]);
                    }

                    $response = $request->get($apiSource->url, $params);

                    // If successful but not processed, return a specific message
                    if ($response->successful()) {
                        return [
                            'success' => false,
                            'message' => 'API responded successfully, but this API type is not configured for processing. Only News API and GNews are currently supported.',
                            'count' => 0
                        ];
                    }
            }

            // If the request failed
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? ('HTTP Status: ' . $response->status());
                Log::error('API Fetch Failed: ' . $errorMessage, [
                    'api_name' => $apiSource->name,
                    'status_code' => $response->status()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to fetch from API: ' . $errorMessage,
                    'count' => 0
                ];
            }

            $data = $response->json();
            $count = 0;

            // Process articles based on API source
            switch ($apiSource->name) {
                case 'News API':
                    if (!empty($data['articles'])) {
                        $count = $this->processNewsApiArticles($data['articles'], $apiSource);
                    }
                    break;

                case 'GNews':
                    if (!empty($data['articles'])) {
                        $count = $this->processGNewsArticles($data['articles'], $apiSource);
                    }
                    break;
            }

            // Update API source record
            $apiSource->update([
                'last_sync' => now(),
                'news_count' => $apiSource->news_count + $count
            ]);

            return [
                'success' => true,
                'message' => "Berhasil menambahkan {$count} berita baru dari {$apiSource->name}.",
                'count' => $count
            ];
        } catch (Exception $e) {
            Log::error('API Fetch Error: ' . $e->getMessage(), [
                'api_name' => $apiSource->name,
                'url' => $apiSource->url
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'count' => 0
            ];
        }
    }

    /**
     * Process articles from News API
     *
     * @param array $articles
     * @param ApiSource $apiSource
     * @return int
     */
    private function processNewsApiArticles(array $articles, ApiSource $apiSource): int
    {
        $count = 0;

        foreach ($articles as $article) {
            // Skip if title or description is empty
            if (empty($article['title']) || empty($article['description'])) {
                continue;
            }

            // Skip if image is empty
            if (empty($article['urlToImage'])) {
                continue;
            }

            // Check if news already exists
            $exists = News::where('title', $article['title'])
                      ->orWhere('api_id', $article['url'] ?? null)
                      ->exists();

            if ($exists) {
                continue;
            }

            // Get or create category
            $categoryName = $article['source']['name'] ?? 'General';
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            // Create new news
            News::create([
                'title' => $article['title'],
                'slug' => $this->generateUniqueSlug($article['title']),
                'content' => $article['description'],
                'image_url' => $article['urlToImage'],
                'source' => $article['source']['name'] ?? $apiSource->name,
                'source_url' => $article['url'] ?? null,
                'api_id' => $article['url'] ?? null,
                'category_id' => $category->id,
                'status' => 'published', // Set status to published for API articles
                'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt']) : now(),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Process articles from GNews
     *
     * @param array $articles
     * @param ApiSource $apiSource
     * @return int
     */
    private function processGNewsArticles(array $articles, ApiSource $apiSource): int
    {
        $count = 0;

        foreach ($articles as $article) {
            // Skip if title or description is empty
            if (empty($article['title']) || empty($article['description'])) {
                continue;
            }

            // Skip if image is empty
            if (empty($article['image'])) {
                continue;
            }

            // Check if news already exists
            $exists = News::where('title', $article['title'])
                      ->orWhere('api_id', $article['url'] ?? null)
                      ->exists();

            if ($exists) {
                continue;
            }

            // Get or create category
            // GNews provides sources object with name
            $categoryName = $article['source']['name'] ?? 'General';
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            // Create new news
            News::create([
                'title' => $article['title'],
                'slug' => $this->generateUniqueSlug($article['title']),
                'content' => $article['description'],
                'image_url' => $article['image'],
                'source' => $article['source']['name'] ?? $apiSource->name,
                'source_url' => $article['url'] ?? null,
                'api_id' => $article['url'] ?? null,
                'category_id' => $category->id,
                'status' => 'published', // Set status to published for API articles
                'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt']) : now(),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Generate a unique slug for a news title
     *
     * @param string $title
     * @return string
     */
    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (News::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Fetch news from all active API sources
     *
     * @return array
     */
    public function fetchFromAllActiveSources(): array
    {
        $results = [];
        $totalCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        $activeSources = ApiSource::where('status', 'active')->get();

        foreach ($activeSources as $source) {
            try {
                $result = $this->fetchFromApi($source);
                $results[$source->name] = $result;
                $totalCount += $result['count'];

                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = $source->name . ': ' . $result['message'];
                }
            } catch (Exception $e) {
                $errorCount++;
                $errors[] = $source->name . ': ' . $e->getMessage();
                Log::error('Error in fetchFromAllActiveSources: ' . $e->getMessage(), [
                    'api_name' => $source->name
                ]);
            }
        }

        return [
            'sources' => $results,
            'total_count' => $totalCount,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ];
    }
}

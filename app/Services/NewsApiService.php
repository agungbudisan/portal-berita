<?php

namespace App\Services;

use App\Models\ApiSource;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class NewsApiService
{
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

            // Only allow News API and GNews API
            if ($apiSource->name !== 'News API' && $apiSource->name !== 'GNews') {
                return [
                    'success' => false,
                    'message' => 'Only News API and GNews API are enabled.',
                    'count' => 0
                ];
            }

            // Make the request to the API with correct parameters based on API source
            $response = null;

            switch ($apiSource->name) {
                case 'News API':
                    $response = Http::withHeaders([
                        'X-Api-Key' => $apiSource->api_key,
                    ])->get($apiSource->url, [
                        'country' => 'us', // US news
                        'pageSize' => 20,
                    ]);
                    break;

                case 'GNews':
                    $response = Http::get($apiSource->url, [
                        'apikey' => $apiSource->api_key,
                        'category' => 'general',
                        'lang' => 'en', // English language
                        'country' => 'us', // US
                        'max' => 10, // Limit to 10 news
                    ]);
                    break;

                default:
                    return [
                        'success' => false,
                        'message' => 'Unknown API source type.',
                        'count' => 0
                    ];
            }

            // If the request failed
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch from API: ' . ($response->json()['message'] ?? 'Unknown error'),
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
                'message' => "Successfully added {$count} new articles.",
                'count' => $count
            ];
        } catch (Exception $e) {
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
            $exists = News::where('title', $article['title'])->exists();
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
                'image' => $article['urlToImage'],
                'source' => $article['source']['name'] ?? $apiSource->name,
                'source_url' => $article['url'] ?? null,
                'api_id' => $article['url'] ?? null,
                'category_id' => $category->id,
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
            $exists = News::where('title', $article['title'])->exists();
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
                'image' => $article['image'],
                'source' => $article['source']['name'] ?? $apiSource->name,
                'source_url' => $article['url'] ?? null,
                'api_id' => $article['url'] ?? null,
                'category_id' => $category->id,
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

        $activeSources = ApiSource::where('status', 'active')
            ->whereIn('name', ['News API', 'GNews'])
            ->get();

        foreach ($activeSources as $source) {
            $result = $this->fetchFromApi($source);
            $results[$source->name] = $result;
            $totalCount += $result['count'];
        }

        return [
            'sources' => $results,
            'total_count' => $totalCount
        ];
    }
}

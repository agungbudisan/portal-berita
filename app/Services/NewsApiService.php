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

                case 'Guardian API':
                    $response = Http::get($apiSource->url, [
                        'api-key' => $apiSource->api_key,
                        'section' => 'world',
                        'page-size' => 20,
                    ]);
                    break;

                case 'News Data IO':
                    $response = Http::get($apiSource->url, [
                        'apikey' => $apiSource->api_key,
                        'country' => 'us', // US news
                        'language' => 'en', // English
                        'size' => 20,
                    ]);
                    break;

                case 'GNews':
                    $response = Http::get($apiSource->url, [
                        'apikey' => $apiSource->api_key,
                        'category' => 'general',
                        'lang' => 'en', // English language
                        'country' => 'us', // US
                        'max' => 20, // Limit to 20 news
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

                case 'Guardian API':
                    if (!empty($data['response']['results'])) {
                        $count = $this->processGuardianArticles($data['response']['results'], $apiSource);
                    }
                    break;

                case 'News Data IO':
                    if (!empty($data['results'])) {
                        $count = $this->processNewsDataIoArticles($data['results'], $apiSource);
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
                'content' => $article['description'] . "\n\n" . ($article['content'] ?? ''),
                'image' => $article['urlToImage'] ?? null,
                'source' => $article['source']['name'] ?? $apiSource->name,
                'api_id' => $article['url'] ?? null,
                'category_id' => $category->id,
                'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt']) : now(),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Process articles from Guardian API
     *
     * @param array $articles
     * @param ApiSource $apiSource
     * @return int
     */
    private function processGuardianArticles(array $articles, ApiSource $apiSource): int
    {
        $count = 0;

        foreach ($articles as $article) {
            // Skip if webTitle is empty
            if (empty($article['webTitle'])) {
                continue;
            }

            // Check if news already exists
            $exists = News::where('title', $article['webTitle'])->exists();
            if ($exists) {
                continue;
            }

            // Get or create category
            $categoryName = $article['sectionName'] ?? 'General';
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            // Create new news
            News::create([
                'title' => $article['webTitle'],
                'slug' => $this->generateUniqueSlug($article['webTitle']),
                'content' => $article['webTitle'] . "\n\n" . "Read more at: " . ($article['webUrl'] ?? ''),
                'image' => null, // Guardian API doesn't provide image in the basic response
                'source' => 'The Guardian',
                'api_id' => $article['id'] ?? null,
                'category_id' => $category->id,
                'published_at' => isset($article['webPublicationDate']) ? Carbon::parse($article['webPublicationDate']) : now(),
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Process articles from News Data IO
     *
     * @param array $articles
     * @param ApiSource $apiSource
     * @return int
     */
    private function processNewsDataIoArticles(array $articles, ApiSource $apiSource): int
    {
        $count = 0;

        foreach ($articles as $article) {
            // Skip if title or description is empty
            if (empty($article['title']) || empty($article['description'])) {
                continue;
            }

            // Check if news already exists
            $exists = News::where('title', $article['title'])->exists();
            if ($exists) {
                continue;
            }

            // Get or create category
            $categoryName = 'General';
            if (!empty($article['category'])) {
                // News Data IO returns category as an array
                if (is_array($article['category']) && !empty($article['category'])) {
                    $categoryName = $article['category'][0] ?? 'General';
                } else {
                    $categoryName = $article['category'] ?? 'General';
                }
            }

            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            // Create new news
            News::create([
                'title' => $article['title'],
                'slug' => $this->generateUniqueSlug($article['title']),
                'content' => $article['description'] . "\n\n" . ($article['content'] ?? ''),
                'image' => $article['image_url'] ?? null, // News Data IO uses image_url
                'source' => $article['source_id'] ?? $apiSource->name,
                'api_id' => $article['article_id'] ?? null,
                'category_id' => $category->id,
                'published_at' => isset($article['pubDate']) ? Carbon::parse($article['pubDate']) : now(),
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
                'content' => $article['description'] . "\n\n" . ($article['content'] ?? ''),
                'image' => $article['image'] ?? null, // GNews uses 'image' field
                'source' => $article['source']['name'] ?? $apiSource->name,
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

        $activeSources = ApiSource::where('status', 'active')->get();

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

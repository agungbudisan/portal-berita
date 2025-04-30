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

            // Make the request to the API
            $response = Http::withHeaders([
                'X-Api-Key' => $apiSource->api_key,
            ])->get($apiSource->url, [
                'country' => 'id', // For Indonesian news
                'pageSize' => 20,
            ]);

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

            // Process the articles
            if (!empty($data['articles'])) {
                foreach ($data['articles'] as $article) {
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
                    $categoryName = $article['source']['name'] ?? 'Umum';
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

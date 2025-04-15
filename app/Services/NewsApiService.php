<?php

namespace App\Services;

use App\Models\ApiConfiguration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsApiService
{
    protected $config;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->config = ApiConfiguration::where('is_active', true)->first();

        if ($this->config) {
            $this->baseUrl = $this->config->base_url;
            $this->apiKey = $this->config->api_key;
        }
    }

    // Untuk News API
    public function getTopHeadlines($category = null, $page = 1, $pageSize = 10)
    {
        $cacheKey = "news_headlines_{$category}_{$page}_{$pageSize}";

        return Cache::remember($cacheKey, 3600, function () use ($category, $page, $pageSize) {
            $url = "{$this->baseUrl}/top-headlines";
            $params = [
                'apiKey' => $this->apiKey,
                'country' => 'id', // Kode negara (id untuk Indonesia)
                'page' => $page,
                'pageSize' => $pageSize
            ];

            if ($category) {
                $params['category'] = $category;
            }

            $response = Http::get($url, $params);

            return $response->json();
        });
    }

    // Untuk The Guardian API
    public function getGuardianNews($section = null, $page = 1, $pageSize = 10)
    {
        $cacheKey = "guardian_news_{$section}_{$page}_{$pageSize}";

        return Cache::remember($cacheKey, 3600, function () use ($section, $page, $pageSize) {
            $url = "{$this->baseUrl}/search";
            $params = [
                'api-key' => $this->apiKey,
                'page' => $page,
                'page-size' => $pageSize,
                'show-fields' => 'headline,trailText,thumbnail,bodyText,publication,lastModified'
            ];

            if ($section) {
                $params['section'] = $section;
            }

            $response = Http::get($url, $params);
            $data = $response->json();

            // Transform data to match our standard format
            $transformedData = [
                'status' => 'ok',
                'totalResults' => $data['response']['total'] ?? 0,
                'articles' => []
            ];

            foreach ($data['response']['results'] ?? [] as $item) {
                $transformedData['articles'][] = [
                    'source' => [
                        'id' => 'guardian',
                        'name' => 'The Guardian'
                    ],
                    'author' => $item['fields']['publication'] ?? 'The Guardian',
                    'title' => $item['fields']['headline'] ?? '',
                    'description' => $item['fields']['trailText'] ?? '',
                    'url' => $item['webUrl'] ?? '',
                    'urlToImage' => $item['fields']['thumbnail'] ?? '',
                    'publishedAt' => $item['webPublicationDate'] ?? '',
                    'content' => $item['fields']['bodyText'] ?? ''
                ];
            }

            return $transformedData;
        });
    }

    public function searchNews($query, $page = 1, $pageSize = 10)
    {
        $cacheKey = "news_search_{$query}_{$page}_{$pageSize}";

        return Cache::remember($cacheKey, 3600, function () use ($query, $page, $pageSize) {
            $response = Http::get("{$this->baseUrl}/everything", [
                'apiKey' => $this->apiKey,
                'q' => $query,
                'page' => $page,
                'pageSize' => $pageSize,
                'sortBy' => 'publishedAt'
            ]);

            return $response->json();
        });
    }

    public function getNewsByCategory($category, $page = 1, $pageSize = 10)
    {
        return $this->getTopHeadlines($category, $page, $pageSize);
    }
}

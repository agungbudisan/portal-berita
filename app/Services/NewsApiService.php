<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\ApiConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    protected $apiConfig;

    public function __construct()
    {
        $this->apiConfig = ApiConfig::where('is_active', true)->first();

        if (!$this->apiConfig) {
            // Fallback ke config .env jika tidak ada di database
            $this->apiConfig = new ApiConfig([
                'api_key' => env('NEWS_API_KEY'),
                'base_url' => env('NEWS_API_BASE_URL', 'https://newsapi.org/v2'),
                'default_params' => json_encode(['language' => 'id']),
                'cache_time_minutes' => 30
            ]);
        }
    }

    public function getHeadlines($params = [])
    {
        if (empty($this->apiConfig->api_key)) {
            Log::error('News API key tidak ditemukan');
            return ['error' => 'API key tidak dikonfigurasi', 'articles' => []];
        }

        $cacheKey = 'news_api_headlines_' . md5(json_encode($params));

        try {
            return Cache::remember($cacheKey, $this->apiConfig->cache_time_minutes * 60, function () use ($params) {
                $defaultParams = json_decode($this->apiConfig->default_params, true) ?? [];
                $mergedParams = array_merge($defaultParams, $params, [
                    'apiKey' => $this->apiConfig->api_key,
                ]);

                $response = Http::get($this->apiConfig->base_url . '/top-headlines', $mergedParams);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('News API error: ' . $response->body());
                return ['error' => 'Error fetching news', 'status' => $response->status(), 'articles' => []];
            });
        } catch (\Exception $e) {
            Log::error('News API exception: ' . $e->getMessage());
            return ['error' => 'Exception while fetching news: ' . $e->getMessage(), 'articles' => []];
        }
    }

    public function searchNews($params = [])
    {
        if (empty($this->apiConfig->api_key)) {
            Log::error('News API key tidak ditemukan');
            return ['error' => 'API key tidak dikonfigurasi', 'articles' => []];
        }

        $cacheKey = 'news_api_search_' . md5(json_encode($params));

        try {
            return Cache::remember($cacheKey, $this->apiConfig->cache_time_minutes * 60, function () use ($params) {
                $defaultParams = json_decode($this->apiConfig->default_params, true) ?? [];
                $mergedParams = array_merge($defaultParams, $params, [
                    'apiKey' => $this->apiConfig->api_key,
                ]);

                $response = Http::get($this->apiConfig->base_url . '/everything', $mergedParams);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('News API error: ' . $response->body());
                return ['error' => 'Error searching news', 'status' => $response->status(), 'articles' => []];
            });
        } catch (\Exception $e) {
            Log::error('News API exception: ' . $e->getMessage());
            return ['error' => 'Exception while searching news: ' . $e->getMessage(), 'articles' => []];
        }
    }
}


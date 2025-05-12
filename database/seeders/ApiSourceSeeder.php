<?php

namespace Database\Seeders;

use App\Models\ApiSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class ApiSourceSeeder extends Seeder
{
    public function run(): void
    {
        ApiSource::create([
            'name' => 'News API',
            'url' => Config::get('services.news_api.url', 'https://newsapi.org/v2/top-headlines'),
            'api_key' => env('NEWS_API_KEY', ''),
            'status' => 'active',
        ]);

        ApiSource::create([
            'name' => 'GNews',
            'url' => Config::get('services.gnews.url', 'https://gnews.io/api/v4/top-headlines'),
            'api_key' => env('GNEWS_API_KEY', ''),
            'status' => 'active',
        ]);
    }
}

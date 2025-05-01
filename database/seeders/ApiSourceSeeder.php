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
            'name' => 'Guardian API',
            'url' => Config::get('services.guardian_api.url', 'https://content.guardianapis.com/search'),
            'api_key' => env('GUARDIAN_API_KEY', ''),
            'status' => 'active',
        ]);

        ApiSource::create([
            'name' => 'News Data IO',
            'url' => Config::get('services.news_data_io.url', 'https://newsdata.io/api/1/latest'),
            'api_key' => env('NEWS_DATA_IO_API_KEY', ''),
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

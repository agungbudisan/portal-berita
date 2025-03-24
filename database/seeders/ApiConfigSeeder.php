<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiConfig;

class ApiConfigSeeder extends Seeder
{
    public function run(): void
    {
        ApiConfig::create([
            'name' => 'News API',
            'api_key' => env('NEWS_API_KEY'),
            'base_url' => env('NEWS_API_BASE_URL', 'https://newsapi.org/v2'),
            'default_params' => json_encode([
                'language' => 'id',
                'pageSize' => 20
            ]),
            'is_active' => true,
            'cache_time_minutes' => 30
        ]);
    }
}

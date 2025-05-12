<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\ApiSource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Create API Sources
        $this->createApiSources();

        // Create Categories
        $this->createCategories();

        // Create Users (1 admin + 1 regular user)
        $this->createUsers($faker);

        // Output summary
        $this->command->info('Database seeded successfully with:');
        $this->command->info('- ' . ApiSource::count() . ' API sources');
        $this->command->info('- ' . Category::count() . ' categories');
        $this->command->info('- ' . User::count() . ' users');
    }

    /**
     * Create API sources
     */
    private function createApiSources(): void
    {
        ApiSource::create([
            'name' => 'News API',
            'url' => Config::get('services.news_api.url', 'https://newsapi.org/v2/top-headlines'),
            'api_key' => env('NEWS_API_KEY', 'sample-key'),
            'status' => 'active',
        ]);

        ApiSource::create([
            'name' => 'GNews',
            'url' => Config::get('services.gnews.url', 'https://gnews.io/api/v4/top-headlines'),
            'api_key' => env('GNEWS_API_KEY', 'sample-key'),
            'status' => 'active',
        ]);
    }

    /**
     * Create categories
     */
    private function createCategories(): void
    {
        $categoryNames = [
            'Politik',
            'Ekonomi',
            'Teknologi',
            'Olahraga',
            'Hiburan',
            'Kesehatan',
            'Pendidikan',
            'Internasional',
            'Bisnis',
            'Gaya Hidup'
        ];

        foreach ($categoryNames as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }

    /**
     * Create users
     */
    private function createUsers($faker): void
    {
        // Create admin
        User::create([
            'name' => 'Admin Winnicode',
            'email' => 'admin@winnicode.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
        ]);

        // Create regular user
        User::create([
            'name' => 'Teguh Setiawan',
            'email' => 'user@winninews.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => Carbon::now(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\News;
use App\Models\Comment;
use App\Models\Bookmark;
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
        $categories = $this->createCategories();

        // Create Users (1 admin + 20 regular users)
        $users = $this->createUsers($faker, 20);

        // Create News (50 articles distributed across categories with varying dates)
        $news = $this->createNews($faker, $categories, 50);

        // Create Comments (average 3-8 comments per news article)
        $this->createComments($faker, $users, $news);

        // Create Bookmarks (some users bookmark multiple articles)
        $this->createBookmarks($users, $news);

        // Output summary
        $this->command->info('Database seeded successfully with:');
        $this->command->info('- ' . ApiSource::count() . ' API sources');
        $this->command->info('- ' . Category::count() . ' categories');
        $this->command->info('- ' . User::count() . ' users');
        $this->command->info('- ' . News::count() . ' news articles');
        $this->command->info('- ' . Comment::count() . ' comments');
        $this->command->info('- ' . Bookmark::count() . ' bookmarks');
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
    private function createCategories(): array
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

        $categories = [];

        foreach ($categoryNames as $category) {
            $categories[] = Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }

        return $categories;
    }

    /**
     * Create users
     */
    private function createUsers($faker, $count): array
    {
        $users = [];

        // Create admin
        $users[] = User::create([
            'name' => 'Admin Winnicode',
            'email' => 'admin@winnicode.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now()->subMonths(rand(1, 6)),
        ]);

        // Create regular users
        for ($i = 0; $i < $count; $i++) {
            $users[] = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => rand(0, 5) > 0 ? Carbon::now() : null, // 80% verified
                'created_at' => Carbon::now()->subDays(rand(1, 180)), // Users created over the last 6 months
            ]);
        }

        return $users;
    }

    /**
     * Create news articles
     */
    private function createNews($faker, $categories, $count): array
    {
        $news = [];
        $sources = ['CNN Indonesia', 'Detik', 'Kompas', 'Tempo', 'Republika', 'BBC Indonesia', 'CNBC Indonesia', 'Antara News'];

        // Distribution of article dates for trend analysis
        $dateDistribution = [
            'today' => 5,
            'this_week' => 15,
            'this_month' => 15,
            'last_month' => 10,
            'older' => 5
        ];

        // Create some local content
        for ($i = 0; $i < $count; $i++) {
            $title = $faker->sentence(rand(6, 12));
            $slug = Str::slug($title);
            $content = $faker->paragraph(rand(20, 40));
            $category = $categories[array_rand($categories)];
            $source = $sources[array_rand($sources)];

            // Determine publication date based on distribution
            $rand = rand(1, array_sum($dateDistribution));
            $publishedAt = Carbon::now();

            if ($rand <= $dateDistribution['today']) {
                // Today
                $publishedAt = Carbon::now()->subHours(rand(0, 12));
            } elseif ($rand <= $dateDistribution['today'] + $dateDistribution['this_week']) {
                // This week
                $publishedAt = Carbon::now()->subDays(rand(1, 7));
            } elseif ($rand <= $dateDistribution['today'] + $dateDistribution['this_week'] + $dateDistribution['this_month']) {
                // This month
                $publishedAt = Carbon::now()->subDays(rand(8, 30));
            } elseif ($rand <= $dateDistribution['today'] + $dateDistribution['this_week'] + $dateDistribution['this_month'] + $dateDistribution['last_month']) {
                // Last month
                $publishedAt = Carbon::now()->subDays(rand(31, 60));
            } else {
                // Older
                $publishedAt = Carbon::now()->subDays(rand(61, 180));
            }

            // Some articles are from API, some are local
            $isFromApi = rand(0, 2) == 0; // 1/3 chance

            $newsItem = News::create([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'image' => $isFromApi ? 'https://source.unsplash.com/random/800x600?sig=' . $i : null, // API news have external URLs
                'source' => $isFromApi ? $source : null,
                'source_url' => $isFromApi ? 'https://example.com/news/' . $slug : null,
                'api_id' => $isFromApi ? 'api-' . Str::random(10) : null,
                'category_id' => $category->id,
                'published_at' => $publishedAt,
                'created_at' => $publishedAt,
                'updated_at' => $publishedAt,
            ]);

            $news[] = $newsItem;
        }

        return $news;
    }

    /**
     * Create comments
     */
    private function createComments($faker, $users, $news): void
    {
        // Each news article gets a random number of comments
        foreach ($news as $article) {
            $commentCount = rand(0, 15); // Some articles have no comments, some have many

            for ($i = 0; $i < $commentCount; $i++) {
                $user = $users[array_rand($users)];
                $status = ['pending', 'approved', 'approved', 'approved']; // 75% approved, 25% pending

                // Comments are usually within 7 days after article publication
                $commentDate = (clone $article->published_at)->addDays(rand(0, 7))->addHours(rand(1, 24));

                // Don't create future comments
                if ($commentDate > Carbon::now()) {
                    $commentDate = Carbon::now()->subHours(rand(1, 48));
                }

                Comment::create([
                    'user_id' => $user->id,
                    'news_id' => $article->id,
                    'content' => $faker->paragraph(rand(1, 3)),
                    'status' => $status[array_rand($status)],
                    'created_at' => $commentDate,
                    'updated_at' => $commentDate,
                ]);
            }
        }
    }

    /**
     * Create bookmarks
     */
    private function createBookmarks($users, $news): void
    {
        // Each user bookmarks some articles
        foreach ($users as $user) {
            // Create a copy of the news array to shuffle and pick from
            $availableNews = $news;
            shuffle($availableNews);

            // Determine how many bookmarks this user will have
            $bookmarkCount = rand(0, 10); // Some users have no bookmarks, some have many

            // Make sure we don't try to create more bookmarks than available news
            $bookmarkCount = min($bookmarkCount, count($availableNews));

            // Take the first N news items after shuffling
            $selectedNews = array_slice($availableNews, 0, $bookmarkCount);

            // Create a bookmark for each selected news item
            foreach ($selectedNews as $newsItem) {
                $bookmarkDate = Carbon::now()->subDays(rand(0, 60));

                Bookmark::create([
                    'user_id' => $user->id,
                    'news_id' => $newsItem->id,
                    'created_at' => $bookmarkDate,
                    'updated_at' => $bookmarkDate,
                ]);
            }
        }
    }
}

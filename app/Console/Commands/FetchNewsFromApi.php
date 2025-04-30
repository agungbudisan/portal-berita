<?php

namespace App\Console\Commands;

use App\Services\NewsApiService;
use Illuminate\Console\Command;

class FetchNewsFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from all active API sources';

    /**
     * Execute the console command.
     */
    public function handle(NewsApiService $newsApiService)
    {
        $this->info('Fetching news from API sources...');

        $result = $newsApiService->fetchFromAllActiveSources();

        $this->info("Fetched {$result['total_count']} new articles in total.");

        foreach ($result['sources'] as $sourceName => $sourceResult) {
            if ($sourceResult['success']) {
                $this->info("- {$sourceName}: {$sourceResult['count']} articles added.");
            } else {
                $this->error("- {$sourceName}: {$sourceResult['message']}");
            }
        }

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Interfaces\NewsApiServiceInterface;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Console\Command;

class FetchNewsApiSourcesCommand extends Command
{
    protected $signature = 'fetch:news-api-sources';

    protected $description = 'This command will fetch news api sources';

    public function handle(NewsApiServiceInterface $newsApiService): void
    {
        $data = $newsApiService->sources();

        if ($data['status'] == "ok") {
            foreach ($data['sources'] as $source) {
                $category = Category::firstOrCreate([
                    'name' => $source['category'] ?? 'general'
                ]);

                Source::firstOrCreate([
                    'name' => $source['name'],
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}

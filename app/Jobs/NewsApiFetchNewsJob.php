<?php

namespace App\Jobs;

use App\Interfaces\NewsApiServiceInterface;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewsApiFetchNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        NewsApiServiceInterface $newsApiService
    ): void
    {
        $data = $newsApiService->news();

        if ($data['status'] !== "ok") {
            \Log::error("NewsApi fetch news failed", [
                'message' => $data['message']
            ]);
            return ;
        }

        foreach ($data['articles'] as $news) {
            $source = Source::firstOrCreate([
                'name' => $news['source']['name'] ?? 'NewsAPI',
            ]);

            if (!$source->category_id) {
                $source->category_id = Category::firstOrCreate(['name' => 'general'])->id;
                $source->save();
                $source->refresh();
            }

            $author = new Author();
            if ($news['author']) {
                $author = Author::firstOrCreate([
                    'name' => $news['author'],
                ]);
            }

            Article::create([
                'source_id' => $source->id,
                'author_id' => $author->id,
                'category_id' => $source->category_id,
                'title' => $news['title'],
                'description' => $news['description'],
                'url' => $news['url'],
                'published_at' => Carbon::parse($news['publishedAt'])->toDateTimeString(),
            ]);
        }
    }
}

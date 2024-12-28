<?php

namespace App\Jobs;

use App\Interfaces\NYTimesServiceInterface;
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

class NYTimesFetchNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(
        NYTimesServiceInterface $NYTimesServiceInterface
    ): void
    {
        $data = $NYTimesServiceInterface->news();

        if (@$data['fault']) {
            \Log::error("NYTimes fetch news failed", [
                'message' => $data['fault']['faultstring']
            ]);
            return ;
        }

        $source = Source::firstOrCreate([
            'name' => 'The New York Times',
        ]);

        foreach ($data['results'] as $news) {
            $category = Category::firstOrCreate([
                'name' => !empty($news['subsection']) ? $news['subsection'] : ((!empty($news['section']) ? $news['section'] : 'general')),
            ]);

            $author = new Author();
            $authorName = \Str::of($news['byline'])->remove('By ')->toString();

            if ($authorName) {
                $author = Author::firstOrCreate([
                    'name' => $authorName,
                ]);
            }

            Article::create([
                'source_id' => $source->id,
                'author_id' => $author->id,
                'category_id' => $category->id,
                'title' => $news['title'],
                'description' => $news['abstract'],
                'url' => $news['url'],
                'published_at' => Carbon::parse($news['published_date'])->toDateTimeString(),
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\NewsApiFetchNewsJob;
use App\Jobs\NYTimesFetchNewsJob;
use Illuminate\Console\Command;

class FetchLatestNewsCommand extends Command
{
    protected $signature = 'fetch:latest-news';

    protected $description = 'fetching latest news from different platforms';

    public function handle(): void
    {
        NewsApiFetchNewsJob::dispatch();
        NYTimesFetchNewsJob::dispatch();
    }
}

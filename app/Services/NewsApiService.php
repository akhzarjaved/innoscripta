<?php

namespace App\Services;

use App\Interfaces\NewsApiServiceInterface;
use Illuminate\Support\Facades\Http;

class NewsApiService implements NewsApiServiceInterface
{

    public $baseUrl = 'https://newsapi.org/v2';

    private mixed $apiKey;

    public function __construct()
    {
        $this->apiKey = config('news-integrations.newsapi');
    }

    public function news()
    {
        return Http::get($this->baseUrl . "/top-headlines?country=us&apiKey={$this->apiKey}")
            ->json();
    }

    public function sources()
    {
        return Http::get($this->baseUrl . "/top-headlines/sources?apiKey={$this->apiKey}")
            ->json();
    }
}

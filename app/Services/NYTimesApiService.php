<?php

namespace App\Services;

use App\Interfaces\NYTimesServiceInterface;
use Illuminate\Support\Facades\Http;

class NYTimesApiService implements NYTimesServiceInterface
{

    public $baseUrl = 'https://api.nytimes.com/svc/topstories/v2';

    private mixed $apiKey;

    public function __construct()
    {
        $this->apiKey = config('news-integrations.nytimes');
    }

    public function news()
    {
        return Http::get($this->baseUrl . "/home.json?api-key={$this->apiKey}")
            ->json();
    }
}

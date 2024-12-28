<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleSearchRequest;
use App\Http\Resources\ArticleResource;
use App\Interfaces\ArticleRepositoryInterface;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function search(
        ArticleRepositoryInterface $articleRepository,
        ArticleSearchRequest $request
    )
    {
        return ArticleResource::collection($articleRepository->search($request));
    }

    public function preferred(
        ArticleRepositoryInterface $articleRepository
    )
    {
        return ArticleResource::collection($articleRepository->searchPreferred());
    }
}

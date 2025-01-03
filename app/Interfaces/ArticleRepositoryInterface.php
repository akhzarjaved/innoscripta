<?php

namespace App\Interfaces;

interface ArticleRepositoryInterface
{
    public function search($request);
    public function searchPreferred();
    public function find($articleId);
}

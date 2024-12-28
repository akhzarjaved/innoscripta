<?php

namespace App\Repositories;

use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;

class ArticleRepository implements ArticleRepositoryInterface
{

    public function search($request)
    {
        return Article::with(['category', 'source', 'author'])
            ->when($request->keyword, function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            })
            ->when($request->category, function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->when($request->source, function ($query) use ($request) {
                $query->where('source_id', $request->source);
            })
            ->when($request->date, function ($query) use ($request) {
                $query->where(\DB::raw('Date(published_at)'), $request->date);
            })
            ->latest('published_at')
            ->paginate(10);
    }

    public function searchPreferred()
    {
        $preferences = auth()->user()->preferences;

        return Article::with(['category', 'source', 'author'])
            ->when($preferences, function ($query) use ($preferences) {
                $query->whereIn('category_id', $preferences->where('type', 'category')->pluck('value'))
                    ->orWhereIn('source_id', $preferences->where('type', 'source')->pluck('value'))
                    ->orWhereIn('author_id', $preferences->where('type', 'author')->pluck('value'));
            })
            ->latest('published_at')
            ->paginate(10);
    }
}

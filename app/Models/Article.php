<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime'
        ];
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
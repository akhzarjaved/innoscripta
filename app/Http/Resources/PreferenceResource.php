<?php

namespace App\Http\Resources;

use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Preference */
class PreferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}

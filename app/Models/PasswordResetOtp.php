<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $guarded = [''];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}

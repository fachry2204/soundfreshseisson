<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['has_cowriters' => 'boolean'];
    }
}

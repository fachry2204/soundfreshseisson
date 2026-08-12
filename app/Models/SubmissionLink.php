<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionLink extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_verified' => 'boolean'];
    }
}

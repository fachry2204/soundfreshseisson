<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevisionRequest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['fields' => 'array', 'submitted_payload' => 'array', 'deadline_at' => 'datetime', 'completed_at' => 'datetime'];
    }
}

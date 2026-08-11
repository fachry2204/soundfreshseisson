<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    protected $guarded = [];

    protected $hidden = ['nik', 'nik_blind_index'];

    protected function casts(): array
    {
        return ['nik' => 'encrypted', 'birth_date' => 'date'];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}

<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasUlids;

    protected $guarded = [];

    protected $hidden = ['draft_token_hash', 'idempotency_key'];

    protected function casts(): array
    {
        return ['status' => SubmissionStatus::class, 'submitted_at' => 'datetime', 'snapshot' => 'array'];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(ProgramPeriod::class, 'program_period_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function song(): HasOne
    {
        return $this->hasOne(Song::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(SubmissionLink::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function revisionRequests(): HasMany
    {
        return $this->hasMany(RevisionRequest::class);
    }
}

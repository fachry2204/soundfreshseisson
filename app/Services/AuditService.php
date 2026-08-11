<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    public function record(string $action, Model $subject, Request $request, array $metadata = []): void
    {
        AuditLog::create([
            'actor_id' => $request->user()?->id,
            'action' => $action,
            'auditable_type' => $subject->getMorphClass(),
            'auditable_id' => $subject->getKey(),
            'metadata' => $metadata,
            'ip_hash' => hash_hmac('sha256', (string) $request->ip(), config('app.key')),
        ]);
    }
}

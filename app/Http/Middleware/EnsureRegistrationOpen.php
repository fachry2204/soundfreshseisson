<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(
            filter_var(AppSetting::valueFor('registration.disabled', '0'), FILTER_VALIDATE_BOOLEAN),
            423,
            'Pendaftaran sedang ditutup. Silakan kembali lagi saat pendaftaran dibuka.',
        );

        return $next($request);
    }
}

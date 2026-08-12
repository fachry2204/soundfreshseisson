<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            if ($response->getStatusCode() === 419) {
                return back()->with('error', 'Sesi formulir telah kedaluwarsa. Silakan isi dan kirim kembali formulir ini.');
            }

            if ($response->getStatusCode() === 429 && $request->is('registration/*')) {
                return back()->with('error', 'Server sedang menerima terlalu banyak permintaan. Tunggu sebentar lalu coba kembali; data yang sudah diisi tetap tersimpan di halaman ini.');
            }

            if ($response->getStatusCode() === 404 && ($request->is('registration/*') || $request->is('pendaftaran/*'))) {
                return back()->with('error', 'Data upload atau periode pendaftaran tidak ditemukan. Muat ulang halaman dan upload kembali file jika ada.');
            }

            return $response;
        });
    })->create();

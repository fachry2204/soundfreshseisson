<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('app_settings')) {
            config([
                'mail.mailers.smtp.host' => AppSetting::valueFor('mail.host', config('mail.mailers.smtp.host')),
                'mail.mailers.smtp.port' => AppSetting::valueFor('mail.port', config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.username' => AppSetting::valueFor('mail.username', config('mail.mailers.smtp.username')),
                'mail.mailers.smtp.password' => AppSetting::valueFor('mail.password', config('mail.mailers.smtp.password')),
                'mail.mailers.smtp.scheme' => AppSetting::valueFor('mail.encryption') === 'ssl' ? 'smtps' : null,
                'mail.from.address' => AppSetting::valueFor('mail.from_address', config('mail.from.address')),
                'mail.from.name' => AppSetting::valueFor('mail.from_name', config('mail.from.name')),
            ]);
        }
        Vite::prefetch(concurrency: 3);
    }
}

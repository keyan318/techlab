<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Admin login: throttled by IP (see routes/web.php's throttle:5,1) AND by the
        // submitted email, so cycling source IPs doesn't get around the limit — the
        // account itself is protected, not just one address.
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perHour(10)->by('admin-login:'.strtolower((string) $request->input('email')));
        });
    }
}

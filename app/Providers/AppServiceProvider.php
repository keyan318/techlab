<?php

namespace App\Providers;

use App\Database\PostgresConnection;
use App\Database\PostgresConnector;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Connection;
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
        // Remote Postgres pooler: reuse connections safely across requests (see App\Database\PersistentPool).
        $this->app->bind('db.connector.pgsql', PostgresConnector::class);
        Connection::resolverFor('pgsql', fn ($pdo, $database, $prefix, $config) => new PostgresConnection($pdo, $database, $prefix, $config));
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

        // Forgot password: at most 3 emails an hour per address, so nobody can flood
        // someone's inbox with temporary passwords.
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by('password-reset:'.strtolower((string) $request->input('email')));
        });
    }
}

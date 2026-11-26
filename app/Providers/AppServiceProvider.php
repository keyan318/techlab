<?php

namespace App\Providers;

use App\Database\PostgresConnection;
use App\Database\PostgresConnector;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Connection;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
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

        // Every web request: a flood guard, not an everyday limit. Signed-in people are counted
        // per account; guests per IP, kept generous because a whole classroom shares one school IP.
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(300)
                ->by($request->user() ? 'u:'.$request->user()->id : 'ip:'.$request->ip())
                ->response(self::tooMany('You are going a bit fast. Wait a few seconds and try again.'));
        });

        // Astro chat and the AI generators each call NVIDIA, so one account can't spam them.
        RateLimiter::for('ai', function (Request $request) {
            $key = 'ai:'.($request->user()?->id ?? $request->ip());

            return [
                Limit::perMinute(12)->by($key.':m')->response(self::tooMany('Astro needs a short breather. Wait a minute, then ask again.')),
                Limit::perDay(400)->by($key.':d')->response(self::tooMany('You have reached today\'s Astro limit. Come back tomorrow, astronaut!')),
            ];
        });

        // File uploads (course materials, links) per account.
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(20)->by('up:'.($request->user()?->id ?? $request->ip()))
                ->response(self::tooMany('Too many uploads at once. Wait a minute and try again.'));
        });

        // Sign-ups per IP: enough for a full class registering together, not for a bot.
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(40)->by('reg:'.$request->ip())
                ->response(self::tooMany('Too many sign-ups from this network. Wait a minute and try again.'));
        });
    }

    /**
     * A 429 reply: JSON with an `error` (what the chat and studio screens show) for fetch calls,
     * the normal "Too Many Requests" page for everything else.
     */
    private static function tooMany(string $message): \Closure
    {
        return function (Request $request, array $headers) use ($message) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'success' => false, 'kind' => 'rate_limit', 'error' => $message], 429, $headers);
            }

            throw new ThrottleRequestsException($message, null, $headers);
        };
    }
}

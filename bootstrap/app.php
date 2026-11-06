<?php

use App\Http\Middleware\ChargeAiXp;
use App\Http\Middleware\EnsureCourseMember;
use App\Http\Middleware\EnsureEnrolled;
use App\Http\Middleware\EnsureLessonUnlocked;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureTermsAccepted;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Cloudflare (and cloudflared tunnels) terminate TLS and forward plain HTTP to us,
        // so without this Laravel thinks every request is HTTP and generates http:// asset
        // URLs, which browsers block as mixed content on an https:// tunnel page.
        $middleware->trustProxies(at: '*');

        // Students and faculty agree to the Terms before onboarding or anything else.
        $middleware->web(append: [EnsureTermsAccepted::class]);

        $middleware->alias([
            'role' => EnsureRole::class,
            'enrolled' => EnsureEnrolled::class,
            'course.member' => EnsureCourseMember::class,
            'lesson.unlocked' => EnsureLessonUnlocked::class,
            'ai.xp' => ChargeAiXp::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

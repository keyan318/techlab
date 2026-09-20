<?php

use Illuminate\Foundation\DevCommands;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// `php artisan dev`: replace the default "server" process so PHP_CLI_SERVER_WORKERS
// (see .env) is honoured. Laravel ignores it unless `serve` runs with --no-reload.
// Same process name => overrides the framework default; queue/logs/vite are untouched.
DevCommands::artisan('serve --no-reload', 'server');

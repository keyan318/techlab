<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\User;
$user = User::first();
$user->password = bcrypt('secret');
$user->save();
echo 'Password set for user: '.$user->email.PHP_EOL;

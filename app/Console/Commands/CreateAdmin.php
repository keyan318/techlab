<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    use AsksForAdminPassword;

    protected $signature = 'admin:create {email} {--name=Admin}';

    protected $description = 'Create an admin account (there is no sign-up form for admins)';

    public function handle(): int
    {
        $email = trim($this->argument('email'));

        if (User::where('email', $email)->exists()) {
            $this->error("An account with {$email} already exists. To change its password, run: php artisan admin:password {$email}");

            return self::FAILURE;
        }

        if (($password = $this->askNewPassword()) === null) {
            return self::FAILURE;
        }

        User::create([
            'name' => $this->option('name'),
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
        ]);

        $this->info("Admin {$email} created. Sign in at /admin/login.");

        return self::SUCCESS;
    }
}

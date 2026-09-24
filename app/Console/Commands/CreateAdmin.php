<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email} {--name=Admin}';

    protected $description = 'Create an admin account (there is no sign-up form for admins)';

    public function handle(): int
    {
        $email = $this->argument('email');

        if (User::where('email', $email)->exists()) {
            $this->error("An account with {$email} already exists.");

            return self::FAILURE;
        }

        $password = $this->secret('Password (min 12 characters)');
        if (strlen((string) $password) < 12) {
            $this->error('Password is too short.');

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

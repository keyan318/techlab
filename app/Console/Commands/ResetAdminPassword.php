<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * The only way to recover an admin: "Forgot password" never emails admins, so a
 * locked-out admin is reset by someone who can run commands on the server.
 */
class ResetAdminPassword extends Command
{
    use AsksForAdminPassword;

    protected $signature = 'admin:password {email}';

    protected $description = 'Choose a new password for an existing admin account';

    public function handle(): int
    {
        $email = trim($this->argument('email'));
        $admin = User::where('email', $email)->where('role', 'admin')->first();

        if (! $admin) {
            $this->error("There is no admin account with {$email}.");

            return self::FAILURE;
        }

        if (($password = $this->askNewPassword()) === null) {
            return self::FAILURE;
        }

        // Clearing the remember token also signs out any browser still "remembered" with the old password.
        $admin->forceFill(['password' => $password, 'remember_token' => null])->save();

        $this->info("Password changed for {$email}. Sign in at /admin/login.");

        return self::SUCCESS;
    }
}

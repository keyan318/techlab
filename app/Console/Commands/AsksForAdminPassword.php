<?php

namespace App\Console\Commands;

/**
 * Hidden password prompt for the admin commands. The typing is invisible, so it asks
 * twice: a single typo would otherwise save a password nobody knows, and the admin
 * only finds out after their session ends.
 */
trait AsksForAdminPassword
{
    private function askNewPassword(): ?string
    {
        $password = (string) $this->secret('Password (min 12 characters, hidden while you type)');

        if (strlen($password) < 12) {
            $this->error('Password is too short. Nothing was saved.');

            return null;
        }

        if ($password !== (string) $this->secret('Type the same password again')) {
            $this->error('The two passwords are different. Nothing was saved.');

            return null;
        }

        return $password;
    }
}

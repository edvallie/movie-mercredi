<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateAdminPassword extends Command
{
    protected $signature = 'admin:generate-password';
    protected $description = 'Generate a bcrypt hash for ADMIN_PASSWORD_HASH in .env';

    public function handle(): int
    {
        $password = $this->secret('Enter admin password');
        $confirm  = $this->secret('Confirm admin password');

        if ($password !== $confirm) {
            $this->error('Passwords do not match.');
            return 1;
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return 1;
        }

        $hash = Hash::make($password);

        $this->newLine();
        $this->info('Add this line to your .env file:');
        $this->line("ADMIN_PASSWORD_HASH='{$hash}'");
        $this->newLine();

        return 0;
    }
}

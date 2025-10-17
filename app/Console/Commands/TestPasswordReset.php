<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test password reset functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email {$email} not found.");

            return 1;
        }

        try {
            // Test mail configuration
            $this->info('Testing mail configuration...');

            // Generate a test token
            $token = \Illuminate\Support\Str::random(60);

            // Send test notification
            $user->sendPasswordResetNotification($token);

            $this->info('Password reset email sent successfully!');
            $this->info("Check your email ({$email}) for the reset link.");

            if (config('mail.default') === 'log') {
                $this->info('Mail is configured to use LOG driver. Check storage/logs/laravel.log for the email content.');
            }

        } catch (\Exception $e) {
            $this->error('Failed to send password reset email: ' . $e->getMessage());

            return 1;
        }

        return 0;
    }
}

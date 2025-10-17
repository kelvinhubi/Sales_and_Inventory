<?php

// Simple test script to check password reset functionality
// Place this in your routes/web.php temporarily for testing

Route::get('/test-password-reset', function () {
    // Check if all required components exist
    $checks = [
        'Password Reset Token Table' => \Schema::hasTable('password_reset_tokens'),
        'User Model Exists' => class_exists(\App\Models\User::class),
        'ForgotPasswordController Exists' => class_exists(\App\Http\Controllers\Auth\ForgotPasswordController::class),
        'ResetPasswordController Exists' => class_exists(\App\Http\Controllers\Auth\ResetPasswordController::class),
        'Mail Configuration' => !empty(config('mail.mailers.smtp')),
        'Password Reset Config' => !empty(config('auth.passwords.users')),
    ];

    // Test user creation
    $testUser = \App\Models\User::first();
    if (!$testUser) {
        $checks['Test User Available'] = false;
    } else {
        $checks['Test User Available'] = true;
        $checks['Test User Email'] = $testUser->email;
    }

    // Check mail configuration
    $mailConfig = [
        'MAIL_MAILER' => env('MAIL_MAILER'),
        'MAIL_HOST' => env('MAIL_HOST'),
        'MAIL_PORT' => env('MAIL_PORT'),
        'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    ];

    return view('password-reset-test', compact('checks', 'mailConfig'));
})->name('test.password.reset');
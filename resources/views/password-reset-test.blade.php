<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .check { margin: 10px 0; }
        .success { color: green; }
        .error { color: red; }
        .config { background: #f5f5f5; padding: 15px; margin: 20px 0; }
        .test-form { background: #e3f2fd; padding: 20px; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Password Reset System Test</h1>
    
    <h2>System Checks</h2>
    @foreach($checks as $check => $status)
        <div class="check">
            <strong>{{ $check }}:</strong> 
            <span class="{{ $status ? 'success' : 'error' }}">
                {{ $status ? '✓ OK' : '✗ FAILED' }}
            </span>
            @if($check === 'Test User Email' && $status)
                <span>({{ $status }})</span>
            @endif
        </div>
    @endforeach
    
    <h2>Mail Configuration</h2>
    <div class="config">
        @foreach($mailConfig as $key => $value)
            <div><strong>{{ $key }}:</strong> {{ $value ?: 'Not set' }}</div>
        @endforeach
    </div>
    
    <h2>Test Password Reset</h2>
    <div class="test-form">
        <p><strong>Step 1:</strong> Go to <a href="{{ route('password.request') }}" target="_blank">Password Reset Form</a></p>
        <p><strong>Step 2:</strong> Enter a valid email address</p>
        <p><strong>Step 3:</strong> Check your email or logs for the reset link</p>
        <p><strong>Step 4:</strong> Click the reset link and enter a new password</p>
    </div>
    
    <h2>Alternative: Test via Artisan Command</h2>
    <div class="test-form">
        <p>Run in terminal:</p>
        <code>php artisan test:password-reset your-email@example.com</code>
    </div>
    
    <h2>Debug Information</h2>
    <div class="config">
        <div><strong>App URL:</strong> {{ config('app.url') }}</div>
        <div><strong>Mail Driver:</strong> {{ config('mail.default') }}</div>
        <div><strong>Password Reset Expire:</strong> {{ config('auth.passwords.users.expire') }} minutes</div>
        <div><strong>Current Time:</strong> {{ now() }}</div>
    </div>
</body>
</html>
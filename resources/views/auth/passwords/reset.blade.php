<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-password-container {
            max-width: 550px;
            width: 100%;
        }

        .reset-password-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
        }

        .icon-circle i {
            font-size: 40px;
            color: #ffffff;
        }

        .card-header-custom h2 {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .card-header-custom p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin: 0;
        }

        .card-body-custom {
            padding: 40px 30px;
        }

        .form-group-custom {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label-custom {
            font-weight: 500;
            color: #344055;
            margin-bottom: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .form-label-custom i {
            margin-right: 8px;
            color: #667eea;
        }

        .input-group-custom {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a0a0;
            font-size: 18px;
            z-index: 2;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 45px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control-custom.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .invalid-feedback-custom {
            display: block;
            color: #dc3545;
            font-size: 13px;
            margin-top: 8px;
            padding-left: 5px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0a0a0;
            font-size: 18px;
            z-index: 3;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .password-strength {
            margin-top: 10px;
            height: 4px;
            background: #e1e8ed;
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #dc3545;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #ffc107;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: #28a745;
        }

        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-size: 13px;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            padding: 5px 0;
            color: #6c757d;
        }

        .password-requirements li i {
            margin-right: 8px;
            width: 16px;
        }

        .password-requirements li.valid {
            color: #28a745;
        }

        .password-requirements li.valid i {
            color: #28a745;
        }

        .btn-submit-custom {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }

        .btn-submit-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-submit-custom:active {
            transform: translateY(0);
        }

        .btn-submit-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-submit-custom i {
            margin-left: 8px;
        }

        .back-to-login {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e1e8ed;
        }

        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .back-to-login a:hover {
            color: #764ba2;
        }

        .back-to-login a i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .back-to-login a:hover i {
            transform: translateX(-3px);
        }

        @media (max-width: 576px) {
            .card-header-custom {
                padding: 30px 20px;
            }

            .card-body-custom {
                padding: 30px 20px;
            }

            .card-header-custom h2 {
                font-size: 24px;
            }
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <div class="reset-password-card">
            <!-- Header -->
            <div class="card-header-custom">
                <div class="icon-circle">
                    <i class="fas fa-lock"></i>
                </div>
                <h2>Reset Password</h2>
                <p>Create a new, strong password for your account</p>
            </div>

            <!-- Body -->
            <div class="card-body-custom">
                <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email -->
                    <div class="form-group-custom">
                        <label class="form-label-custom">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <div class="input-group-custom">
                            <i class="input-icon fas fa-envelope"></i>
                            <input 
                                id="email" 
                                type="email" 
                                class="form-control-custom @error('email') is-invalid @enderror" 
                                name="email" 
                                value="{{ $email ?? old('email') }}" 
                                required 
                                autocomplete="email" 
                                autofocus
                                readonly
                                style="background: #e9ecef;">
                        </div>
                        @error('email')
                            <span class="invalid-feedback-custom">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="form-group-custom">
                        <label class="form-label-custom">
                            <i class="fas fa-lock"></i>
                            New Password
                        </label>
                        <div class="input-group-custom">
                            <i class="input-icon fas fa-lock"></i>
                            <input 
                                id="password" 
                                type="password" 
                                class="form-control-custom @error('password') is-invalid @enderror" 
                                name="password" 
                                required 
                                autocomplete="new-password"
                                placeholder="Enter new password">
                            <i class="password-toggle fas fa-eye" id="togglePassword"></i>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="password-requirements" id="passwordRequirements">
                            <ul>
                                <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                                <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                                <li id="req-lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                                <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                            </ul>
                        </div>
                        @error('password')
                            <span class="invalid-feedback-custom">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group-custom">
                        <label class="form-label-custom">
                            <i class="fas fa-lock"></i>
                            Confirm Password
                        </label>
                        <div class="input-group-custom">
                            <i class="input-icon fas fa-lock"></i>
                            <input 
                                id="password-confirm" 
                                type="password" 
                                class="form-control-custom" 
                                name="password_confirmation" 
                                required 
                                autocomplete="new-password"
                                placeholder="Confirm new password">
                            <i class="password-toggle fas fa-eye" id="togglePasswordConfirm"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-custom" id="submitBtn">
                        <span id="btnText">Reset Password</span>
                        <i class="fas fa-check-circle"></i>
                        <div class="loading-spinner" id="loadingSpinner"></div>
                    </button>
                </form>

                <div class="back-to-login">
                    <a href="{{ route('Login') }}">
                        <i class="fas fa-arrow-left"></i>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            $('#togglePasswordConfirm').on('click', function() {
                const passwordField = $('#password-confirm');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#strengthBar');
                
                // Check requirements
                const hasLength = password.length >= 8;
                const hasUppercase = /[A-Z]/.test(password);
                const hasLowercase = /[a-z]/.test(password);
                const hasNumber = /[0-9]/.test(password);
                
                // Update requirement indicators
                $('#req-length').toggleClass('valid', hasLength);
                $('#req-uppercase').toggleClass('valid', hasUppercase);
                $('#req-lowercase').toggleClass('valid', hasLowercase);
                $('#req-number').toggleClass('valid', hasNumber);
                
                // Update icons
                $('#req-length i').attr('class', hasLength ? 'fas fa-check-circle' : 'fas fa-circle');
                $('#req-uppercase i').attr('class', hasUppercase ? 'fas fa-check-circle' : 'fas fa-circle');
                $('#req-lowercase i').attr('class', hasLowercase ? 'fas fa-check-circle' : 'fas fa-circle');
                $('#req-number i').attr('class', hasNumber ? 'fas fa-check-circle' : 'fas fa-circle');
                
                // Calculate strength
                let strength = 0;
                if (hasLength) strength++;
                if (hasUppercase) strength++;
                if (hasLowercase) strength++;
                if (hasNumber) strength++;
                
                // Update strength bar
                strengthBar.removeClass('weak medium strong');
                if (strength === 0) {
                    strengthBar.css('width', '0');
                } else if (strength <= 2) {
                    strengthBar.addClass('weak');
                } else if (strength === 3) {
                    strengthBar.addClass('medium');
                } else {
                    strengthBar.addClass('strong');
                }
            });

            // Form submission
            $('#resetPasswordForm').on('submit', function() {
                const submitBtn = $('#submitBtn');
                const btnText = $('#btnText');
                const spinner = $('#loadingSpinner');
                
                submitBtn.prop('disabled', true);
                btnText.text('Resetting...');
                spinner.show();
            });
        });
    </script>
</body>
</html>
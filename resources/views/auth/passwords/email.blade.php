<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - {{ config('app.name') }}</title>
    
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

        .forgot-password-container {
            max-width: 500px;
            width: 100%;
        }

        .forgot-password-card {
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

        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .alert-custom i {
            margin-right: 12px;
            font-size: 20px;
        }

        .alert-success-custom {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
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
        }

        .invalid-feedback-custom {
            display: block;
            color: #dc3545;
            font-size: 13px;
            margin-top: 8px;
            padding-left: 5px;
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
        }

        .btn-submit-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-submit-custom:active {
            transform: translateY(0);
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
            gap: 8px;
        }

        .back-to-login a i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .back-to-login a:hover i {
            transform: translateX(-3px);
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #1976D2;
        }

        .info-box i {
            margin-right: 10px;
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

        .btn-submit-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <!-- Header -->
            <div class="card-header-custom">
                <div class="icon-circle">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Forgot Password?</h2>
                <p>No worries! We'll send you reset instructions.</p>
            </div>

            <!-- Body -->
            <div class="card-body-custom">
                @if (session('status'))
                    <div class="alert-custom alert-success-custom">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    Enter your registered email address and we'll send you a link to reset your password.
                </div>

                <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                    @csrf

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
                                value="{{ old('email') }}" 
                                required 
                                autocomplete="email" 
                                autofocus
                                placeholder="Enter your email address">
                        </div>
                        @error('email')
                            <span class="invalid-feedback-custom">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit-custom" id="submitBtn">
                        <span id="btnText">Send Reset Link</span>
                        <i class="fas fa-paper-plane"></i>
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
            $('#forgotPasswordForm').on('submit', function() {
                const submitBtn = $('#submitBtn');
                const btnText = $('#btnText');
                const spinner = $('#loadingSpinner');
                
                submitBtn.prop('disabled', true);
                btnText.text('Sending...');
                spinner.show();
            });

            // Auto-focus email input
            $('#email').focus();
        });
    </script>
</body>
</html>
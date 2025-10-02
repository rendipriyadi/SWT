<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1" />
    <meta name="description" content="Siemens Safety Walk and Talk - Sistem Pelaporan Masalah Safety dan 5S" />
    <meta name="theme-color" content="#009999" />
    <title>Login - Safety Walk and Talk</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ asset('css/inter-font.css') }}" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}?v={{ time() }}" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .logo-container {
            margin-bottom: 1rem;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .brand-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            margin-top: 0.5rem;
        }
        
        .brand-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
            margin-top: 0.25rem;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-control {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #6b7280;
            box-shadow: 0 0 0 0.2rem rgba(107, 114, 128, 0.15);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
            font-size: 1rem;
            width: 100%;
            transition: all 0.2s ease;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .remember-me input[type="checkbox"] {
            margin-right: 0.5rem;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .test-accounts {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.85rem;
        }
        
        .test-accounts h6 {
            color: #374151;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .test-account {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
            padding: 0.25rem 0;
        }
        
        .test-account:last-child {
            margin-bottom: 0;
        }
        
        .account-role {
            font-weight: 500;
        }
        
        .account-admin {
            color: #6b7280;
        }
        
        .account-user {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    @php
                        $logoPath = file_exists(public_path('images/logo-s.png')) ? 'images/logo-s.png' : (file_exists(public_path('images/logo.png')) ? 'images/logo.png' : 'images/favicon.png');
                    @endphp
                    <img src="{{ asset($logoPath) }}" alt="Siemens" class="logo">
                </div>
                <h1 class="brand-title">Safety Walk and Talk</h1>
                <p class="brand-subtitle">Sistem Pelaporan Masalah Safety dan 5S</p>
            </div>
            
            <div class="login-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-floating">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" placeholder="Email" 
                               value="{{ old('email') }}" required autofocus>
                        <label for="email">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Password" required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <div class="test-accounts">
                    <h6><i class="fas fa-info-circle me-1"></i>Test Accounts:</h6>
                    <div class="test-account">
                        <span class="account-role account-admin">Admin:</span>
                        <span>admin@siemens.com / password123</span>
                    </div>
                    <div class="test-account">
                        <span class="account-role account-user">User:</span>
                        <span>user@siemens.com / password123</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>

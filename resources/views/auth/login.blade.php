<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('', 'SAINS Inventory System - Login Page') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .login-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #3060fe 0%, #1c19c2 100%);
            padding: 2rem 2rem 3rem;
            color: white;
            text-align: center;
            position: relative;
        }
        
        .logo-container {
            background-color: white;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            width: 75px;
            height: auto;
        }
        
        .form-container {
            padding: 2rem;
            margin-top: -1.5rem;
            background-color: white;
            border-radius: 1.5rem 1.5rem 0 0;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .input-field {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .input-field:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            outline: none;
        }
        
        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
        }
        
        .login-button {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #3060fe 0%, #1c19c2 100%);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            text-align: center;
            transition: background-color 0.15s ease-in-out, transform 0.15s ease-in-out;
            border: none;
            cursor: pointer;
            margin-top: 1.5rem;
        }
        
        .login-button:hover {
            background: linear-gradient(135deg, #000ba0 0%, #000076 100%);
            transform: translateY(-1px);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        /* Add this class for hiding elements */
        .hidden {
            display: none !important;
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .forgot-password a {
            color: #4b5563;
            font-size: 0.875rem;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            color: #ef4444;
            text-decoration: underline;
        }
        
        .footer-text {
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 2rem;
        }
        
        .footer-link {
            color: #ef4444;
            text-decoration: none;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .remember-me input {
            margin-right: 0.5rem;
        }
        
        /* Fix for password container */
        .password-container {
            position: relative;
        }
        
        /* Text sizes for header */
        .text-2xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }
        
        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        
        .mt-2 {
            margin-top: 0.5rem;
        }
        
        .opacity-80 {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="{{ asset('image/sains_logo.png') }}" alt="SAINS Logo" class="logo">
                </div>
                <h1 class="text-2xl font-bold">Welcome Back</h1>
                <p class="text-sm opacity-80 mt-2">Sign in to SAINS Inventory System</p>
            </div>
            
            <div class="form-container">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Username Input -->
                    <div class="input-group">
                        <label for="username" class="input-label">Username</label>
                        <input 
                            id="username" 
                            type="text" 
                            name="username" 
                            value="{{ old('username') }}" 
                            class="input-field" 
                            autocomplete="username" 
                            autofocus
                        >
                        <!-- Added hidden class to ensure error message is not visible on load -->
                        <p class="error-message hidden" id="usernameError">Username is required</p>
                        @error('username')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password Input -->
                    <div class="input-group">
                        <label for="password" class="input-label">Password</label>
                        <div class="password-container">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                class="input-field" 
                                autocomplete="current-password"
                            >
                            <!-- Fixed eye icon positioning -->
                            <span class="input-icon" id="togglePassword">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </span>
                        </div>
                        <!-- Added hidden class to ensure error message is not visible on load -->
                        <p class="error-message hidden" id="passwordError">Password is required</p>
                        @error('password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="remember-me">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember" 
                        >
                        <label for="remember_me" class="text-sm text-gray-600">Remember me</label>
                    </div>
                    
                    <!-- Forgot Password Link -->
                    <div class="forgot-password">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                Forgot your password?
                            </a>
                        @endif
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" id="loginButton" class="login-button">
                        Sign In
                    </button>
                    
                    <!-- Footer Text -->
                    <div class="footer-text">
                        <p>© {{ date('Y') }} SAINS. All rights reserved.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let usernameField = document.getElementById("username");
            let passwordField = document.getElementById("password");
            let loginButton = document.getElementById("loginButton");
            let togglePassword = document.getElementById("togglePassword");
            let usernameError = document.getElementById("usernameError");
            let passwordError = document.getElementById("passwordError");

            // Ensure error messages are hidden on page load
            usernameError.classList.add("hidden");
            passwordError.classList.add("hidden");

            // Toggle password visibility
            togglePassword.addEventListener("click", function () {
                const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
                passwordField.setAttribute("type", type);
                
                // Change eye icon based on password visibility
                if (type === "text") {
                    togglePassword.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    `;
                } else {
                    togglePassword.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    `;
                }
            });

            // Form validation
            loginButton.addEventListener("click", function(event) {
                let valid = true;
                
                // Reset error messages
                usernameError.classList.add("hidden");
                passwordError.classList.add("hidden");
                
                if (usernameField.value.trim() === "") {
                    usernameError.classList.remove("hidden");
                    valid = false;
                }
                
                if (passwordField.value.trim() === "") {
                    passwordError.classList.remove("hidden");
                    valid = false;
                }
                
                if (!valid) {
                    event.preventDefault(); // Prevent form submission
                }
            });

            // Hide error messages when user starts typing
            usernameField.addEventListener("input", function() {
                usernameError.classList.add("hidden");
            });
            
            passwordField.addEventListener("input", function() {
                passwordError.classList.add("hidden");
            });

            // Move to password field when Enter is pressed in username field
            usernameField.addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    passwordField.focus();
                }
            });
            
            // Submit form when Enter is pressed in password field
            passwordField.addEventListener("keydown", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    loginButton.click();
                }
            });
        });
    </script>
</body>
</html>
@extends('layouts.layout')

@section('content')
    <div class="container">
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">← Back to Login</a>

        <h2 class="text-2xl font-semibold mb-6">Forgot Password</h2>

        <!-- Display status message if password has been reset -->
        @if(session('password_reset'))
            <div class="mt-4 p-4 bg-green-100 text-green-800 rounded border-l-4 border-green-500">
                {{ session('password_reset') }}
            </div>
        @endif

        <!-- Display the generic status message -->
        @if(session('status'))
            <div class="mt-4 p-4 bg-blue-100 text-blue-800 rounded border-l-4 border-blue-500">
                {{ session('status') }}
            </div>
        @endif

        <!-- Password Reset Form -->
        <form id="passwordResetForm" method="POST" action="{{ route('password.forgot.handle') }}">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required class="input-field">
                @error('username')
                    <p class="text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="btn btn-primary">Request Reset</button>
                <button type="submit"
                    formaction="{{ route('password.recover') }}"
                    formmethod="POST"
                    class="btn btn-secondary">
                    Recover Password
                </button>
                <button type="button" id="checkStatusBtn" class="btn btn-info">Check Status</button>
            </div>
        </form>

        <!-- Status Result Area -->
        <div id="statusResult" class="mt-6 hidden">
            <!-- Results will be loaded here via AJAX -->
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .input-field {
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-info {
            background-color: #17a2b8;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .hidden {
            display: none;
        }

        .status-message {
            padding: 16px;
            margin: 16px 0;
            border-radius: 6px;
            font-size: 1.1rem;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .status-info {
            background-color: #cce5ff;
            color: #004085;
            border-left: 4px solid #007bff;
        }

        .status-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .status-header {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .status-details {
            margin-top: 8px;
            font-size: 1rem;
            padding: 8px;
            background-color: #f1f1f1;
            border-radius: 4px;
        }

        .status-footer {
            margin-top: 12px;
            font-style: italic;
            color: #5a6268;
        }
    </style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkStatusBtn = document.getElementById('checkStatusBtn');
        const statusResult = document.getElementById('statusResult');
        const usernameInput = document.getElementById('username');
        const resetForm = document.getElementById('passwordResetForm');

        // Get CSRF token from meta tag or directly from the form
        function getCsrfToken() {
            // Try to get from meta tag first
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                return metaTag.getAttribute('content');
            }
            
            // Fall back to getting it from the form
            const tokenInput = document.querySelector('input[name="_token"]');
            if (tokenInput) {
                return tokenInput.value;
            }
            
            return null;
        }

        // Add form submission handler
        resetForm.addEventListener('submit', function() {
            const username = usernameInput.value;
            if (username) {
                // Show immediate feedback before form submits
                statusResult.innerHTML = `
                    <div class="p-4 bg-blue-100 text-blue-800 rounded border-l-4 border-blue-500">
                        <p>Your password reset request is being submitted...</p>
                    </div>
                `;
                statusResult.classList.remove('hidden');
            }
        });

        checkStatusBtn.addEventListener('click', function() {
            const username = usernameInput.value.trim();
            
            if (!username) {
                alert('Please enter a username first');
                return;
            }

            // Show loading state
            statusResult.innerHTML = '<div class="p-4 bg-gray-100 text-gray-800 rounded border-l-4 border-gray-500">Checking status...</div>';
            statusResult.classList.remove('hidden');

            // Make AJAX request
            fetch(`/password/check-status?username=${encodeURIComponent(username)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                let html = '';

                if (data.status) {
                    html += `<div class="status-message ${data.status === 'viewed' ? 'status-info' : (data.status === 'pending' ? 'status-warning' : 'status-success')}">`;
                    html += `<div class="status-header">${data.status}</div>`;
                    html += `<div class="status-details">Requested At: ${data.requestedAt}</div>`;

                    if (data.password) {
                        html += `<div class="status-details"><strong>Password:</strong> ${data.password}</div>`;
                    }

                    html += `<div class="status-footer">${data.message}</div>`;
                    html += `</div>`;
                } else {
                    html = `<div class="status-message status-error"><div>No password reset or recovery request found.</div></div>`;
                }

                statusResult.innerHTML = html;
                statusResult.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                statusResult.innerHTML = `<div class="p-4 bg-red-100 text-red-800 rounded border-l-4 border-red-500">An error occurred. Please try again.</div>`;
            });
        });
    });
</script>
@endsection

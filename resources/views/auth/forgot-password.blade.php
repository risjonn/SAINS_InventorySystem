@extends('layouts.layout')

@section('content')
    <div class="container">
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">← Back to Login</a>

        <h2>Forgot Password</h2>

        <!-- Display status message if password has been reset -->
        @if(session('password_reset'))
            <div class="mt-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('password_reset') }}
            </div>
        @endif

        <!-- Display the generic status message -->
        @if(session('status'))
            <div class="mt-4 p-4 bg-blue-100 text-blue-800 rounded">
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
                <button type="button" id="checkStatusBtn" class="btn btn-secondary">Check Status</button>
            </div>
        </form>

        <!-- Status Result Area -->
        <div id="statusResult" class="mt-6 hidden">
            <!-- Results will be loaded here via AJAX -->
        </div>

        <!-- If username was submitted and not using AJAX, show old password reset details -->
        @if(old('username') && !request()->ajax())
            @php
                $user = \App\Models\User::where('username', old('username'))->first();
                $note = $user
                    ? $user->notifications()
                        ->where('type', \App\Notifications\PasswordResetNotice::class)
                        ->latest()
                        ->first()
                    : null;
            @endphp

            @if($note)
                <div class="mt-6 p-4 bg-yellow-100 text-yellow-800 rounded">
                    <p><strong>Password reset on:</strong> {{ $note->data['resetAt'] }}</p>
                    <p><strong>Your new password:</strong> <code>{{ $note->data['newPassword'] }}</code></p>
                    <p>Please log in and change it immediately.</p>
                </div>
            @endif
        @endif
    </div>
@endsection

@section('styles')
    <style>
        .input-field {
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .hidden {
            display: none;
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

        // Add form submission handler
        resetForm.addEventListener('submit', function() {
            const username = usernameInput.value;
            if (username) {
                // Show immediate feedback before form submits
                statusResult.innerHTML = `
                    <div class="p-4 bg-blue-100 text-blue-800 rounded">
                        <p>Your password reset request is being submitted...</p>
                    </div>
                `;
                statusResult.classList.remove('hidden');
            }
        });

        checkStatusBtn.addEventListener('click', function() {
            const username = usernameInput.value;
            
            if (!username) {
                alert('Please enter a username first');
                return;
            }

            // Show loading state
            statusResult.innerHTML = '<div class="p-4 bg-gray-100 text-gray-800 rounded">Checking status...</div>';
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
                if (data.status === 'reset') {
                    // Password was already reset
                    statusResult.innerHTML = `
                        <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
                            <p><strong>Password reset on:</strong> ${data.resetAt}</p>
                            <p><strong>Your new password:</strong> <code>${data.newPassword}</code></p>
                            <p>Please log in and change it immediately.</p>
                        </div>
                    `;
                } else if (data.status === 'pending') {
                    // Password reset is pending
                    statusResult.innerHTML = `
                        <div class="p-4 bg-blue-100 text-blue-800 rounded">
                            <p>Your password reset request is pending. Please wait for an administrator to process it.</p>
                        </div>
                    `;
                } else {
                    // No reset request found
                    statusResult.innerHTML = `
                        <div class="p-4 bg-gray-100 text-gray-800 rounded">
                            <p>No password reset request found. Please use the Request Reset button to submit a new request.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusResult.innerHTML = `
                    <div class="p-4 bg-red-100 text-red-800 rounded">
                        <p>An error occurred while checking your password reset status. Please try again.</p>
                    </div>
                `;
            });
        });
    });
</script>
@endsection
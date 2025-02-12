<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="loginForm" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                type="text" name="username" :value="old('username')" autocomplete="username" />

            <p class="text-red-600 text-sm mt-1 hidden error-message" id="usernameError">Username is required.</p>

            @error('username')
                <p class="text-red-600 text-sm mt-1 error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                type="password" name="password" autocomplete="current-password" />

            <p class="text-red-600 text-sm mt-1 hidden error-message" id="passwordError">Password is required.</p>

            @error('password')
                <p class="text-red-600 text-sm mt-1 error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button id="loginButton" class="ml-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- JavaScript adjustments: update any references from "email" to "username" -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let usernameField = document.getElementById("username");
            let passwordField = document.getElementById("password");
            let loginButton = document.getElementById("loginButton");
            let form = document.getElementById("loginForm");

            let usernameError = document.getElementById("usernameError");
            let passwordError = document.getElementById("passwordError");

            // Function to show validation errors on button click
            function validateForm(event) {
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
                    event.preventDefault(); // Prevent form submission if validation fails
                }
            }

            // Hide error message when the user starts typing
            function hideErrorMessage(inputField, ...errorMessages) {
                inputField.addEventListener("input", function () {
                    errorMessages.forEach(error => error.classList.add("hidden"));
                });
            }

            // Attach validation function to login button
            loginButton.addEventListener("click", validateForm);

            // Hide errors when the user starts typing
            hideErrorMessage(usernameField, usernameError);
            hideErrorMessage(passwordField, passwordError);

            // Move to the password field when pressing Enter in the username field
            usernameField.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    passwordField.focus();
                }
            });

            // Auto-submit the form when pressing Enter in the password field
            passwordField.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    loginButton.click(); // Triggers form validation and submission
                }
            });
        });
    </script>
</x-guest-layout>

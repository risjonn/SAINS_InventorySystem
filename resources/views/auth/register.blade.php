<x-guest-layout>
    <form id="registerForm" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autocomplete="name" />
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="nameError">Name is required.</p>

            @error('name')
                <p class="text-red-600 text-sm mt-1 error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="emailError">Email is required.</p>
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="emailFormatError">Please enter a valid email address.</p>

            @error('email')
                <p class="text-red-600 text-sm mt-1 error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="passwordError">Password is required.</p>

            @error('password')
                <p class="text-red-600 text-sm mt-1 error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="passwordConfirmError">Confirm Password is required.</p>
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="passwordMatchError">Passwords do not match.</p>

            @error('password_confirmation')
                <p class="text-red-600 text-sm mt-1 error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button id="registerButton" class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- JavaScript for Preventing Auto Submit on Enter and Validations -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let nameField = document.getElementById("name");
            let emailField = document.getElementById("email");
            let passwordField = document.getElementById("password");
            let passwordConfirmField = document.getElementById("password_confirmation");
            let registerButton = document.getElementById("registerButton");
            let form = document.getElementById("registerForm");

            let nameError = document.getElementById("nameError");
            let emailError = document.getElementById("emailError");
            let emailFormatError = document.getElementById("emailFormatError");
            let passwordError = document.getElementById("passwordError");
            let passwordConfirmError = document.getElementById("passwordConfirmError");
            let passwordMatchError = document.getElementById("passwordMatchError");

            // Function to validate email format
            function isValidEmail(email) {
                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email);
            }

            // Function to show validation errors on button click
            function validateForm(event) {
                let valid = true;

                // Reset error messages
                document.querySelectorAll(".error-message").forEach(error => error.classList.add("hidden"));

                if (nameField.value.trim() === "") {
                    nameError.classList.remove("hidden");
                    valid = false;
                }

                if (emailField.value.trim() === "") {
                    emailError.classList.remove("hidden");
                    valid = false;
                } else if (!isValidEmail(emailField.value.trim())) {
                    emailFormatError.classList.remove("hidden");
                    valid = false;
                }

                if (passwordField.value.trim() === "") {
                    passwordError.classList.remove("hidden");
                    valid = false;
                }

                if (passwordConfirmField.value.trim() === "") {
                    passwordConfirmError.classList.remove("hidden");
                    valid = false;
                } else if (passwordField.value.trim() !== passwordConfirmField.value.trim()) {
                    passwordMatchError.classList.remove("hidden");
                    valid = false;
                }

                if (!valid) {
                    event.preventDefault(); // Prevent form submission if validation fails
                }
            }

            // Hide error messages when the user starts typing
            function hideErrorMessage(inputField, ...errorMessages) {
                inputField.addEventListener("input", function () {
                    errorMessages.forEach(error => error.classList.add("hidden"));
                });
            }

            // Attach validation function to register button
            registerButton.addEventListener("click", validateForm);

            // Hide errors when the user starts typing
            hideErrorMessage(nameField, nameError);
            hideErrorMessage(emailField, emailError, emailFormatError);
            hideErrorMessage(passwordField, passwordError, passwordMatchError);
            hideErrorMessage(passwordConfirmField, passwordConfirmError, passwordMatchError);

            // Move to the next input field when pressing Enter
            nameField.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    emailField.focus();
                }
            });

            emailField.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    passwordField.focus();
                }
            });

            passwordField.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    passwordConfirmField.focus();
                }
            });

            // Auto-submit the form when pressing Enter in the confirm password field
            passwordConfirmField.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    registerButton.click(); // Triggers form validation and submission
                }
            });
        });
    </script>
</x-guest-layout>

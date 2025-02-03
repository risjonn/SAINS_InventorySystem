<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="passwordResetForm" method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autofocus />
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="emailError">Email is required.</p>
            <p class="text-red-600 text-sm mt-1 hidden error-message" id="emailFormatError">Please enter a valid email address.</p>

            @error('email')
                <p class="text-red-600 text-sm mt-1 error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button id="resetButton">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>

    <!-- JavaScript for Click-Based Validation -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let emailField = document.getElementById("email");
            let resetButton = document.getElementById("resetButton");

            let emailError = document.getElementById("emailError");
            let emailFormatError = document.getElementById("emailFormatError");

            let allErrorMessages = document.querySelectorAll(".error-message");

            // Function to validate email format
            function isValidEmail(email) {
                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email);
            }

            // Function to show validation errors on button click
            function validateForm(event) {
                let valid = true;

                // Reset error messages
                allErrorMessages.forEach(error => error.classList.add("hidden"));

                if (emailField.value.trim() === "") {
                    emailError.classList.remove("hidden");
                    valid = false;
                } else if (!isValidEmail(emailField.value.trim())) {
                    emailFormatError.classList.remove("hidden");
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

            // Attach validation function to reset button
            resetButton.addEventListener("click", validateForm);

            // Hide errors when the user starts typing
            hideErrorMessage(emailField, emailError, emailFormatError);
        });
    </script>
</x-guest-layout>

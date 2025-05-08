@extends('layouts.layout')

<!-- Enhanced Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Modal Header with Enhanced Styling -->
            <div class="modal-header bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-0">
                <h5 class="modal-title font-weight-bold" id="resetPasswordModalLabel">
                    <i class="fas fa-key mr-2"></i>Reset Password
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <!-- Form with Enhanced Styling -->
            <form method="POST" action="{{ route('admin.password.requests.update') }}">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="request_id" id="reset-request-id">
                    
                    <!-- Username Field -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-gray-700 mb-2">
                            <i class="fas fa-user mr-1 text-gray-500"></i> Username
                        </label>
                        <input type="text" class="form-control bg-gray-100 border-0" id="reset-username" readonly>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <small><i class="fas fa-info-circle mr-1"></i> Please enter a new password with at least 6 characters.</small>
                    </div>
                    
                    <!-- Password Field with Requirements -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-gray-700 mb-2" for="new_password">
                            <i class="fas fa-lock mr-1 text-gray-500"></i> New Password
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="input-group-append">
                                <button class="btn" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Confirm Password Field -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-gray-700 mb-2" for="new_password_confirmation">
                            <i class="fas fa-lock mr-1 text-gray-500"></i> Confirm Password
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            <div class="input-group-append">
                                <button class="btn" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Match Indicator -->
                    <div class="text-center mt-3 mb-2 d-none" id="passwordMatch">
                        <span class="badge badge-success px-3 py-2">
                            <i class="fas fa-check mr-1"></i> Passwords match
                        </span>
                    </div>
                    <div class="text-center mt-3 mb-2 d-none" id="passwordMismatch">
                        <span class="badge badge-danger px-3 py-2">
                            <i class="fas fa-times mr-1"></i> Passwords do not match
                        </span>
                    </div>
                </div>
                
                <!-- Enhanced Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4" id="submitResetBtn">
                        <i class="fas fa-check mr-1"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Script for Password Visibility Toggle and Validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('new_password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Toggle confirm password visibility
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('new_password_confirmation');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Check if passwords match
    function checkPasswordMatch() {
        const password = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;
        const matchDiv = document.getElementById('passwordMatch');
        const mismatchDiv = document.getElementById('passwordMismatch');
        
        if (password === '' || confirmPassword === '') {
            matchDiv.classList.add('d-none');
            mismatchDiv.classList.add('d-none');
            return;
        }
        
        if (password === confirmPassword) {
            matchDiv.classList.remove('d-none');
            mismatchDiv.classList.add('d-none');
        } else {
            matchDiv.classList.add('d-none');
            mismatchDiv.classList.remove('d-none');
        }
    }
    
    // Add event listeners for password fields
    document.getElementById('new_password').addEventListener('input', checkPasswordMatch);
    document.getElementById('new_password_confirmation').addEventListener('input', checkPasswordMatch);
    
    // Fix for modal close button
    $('.modal').on('click', '[data-dismiss="modal"]', function() {
        $(this).closest('.modal').modal('hide');
    });
});
</script>
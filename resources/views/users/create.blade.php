@extends('layouts.layout')

@section('title', 'Create New User - Tabbed Layout')

@section('styles')
<style>
  [x-cloak] { display: none !important; }
  .error-message {
    color: #f56565; /* Tailwind red-500 */
    font-size: 0.875rem; /* text-sm */
    margin-top: 0.25rem; /* mt-1 */
    display: flex;
    align-items: center;
  }
  .error-message i {
    margin-right: 0.25rem;
  }
  /* When error is active, override the default focus ring with a red one */
  .error-active:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(245, 101, 101, 0.75) !important;
  }
  /* Progress bar styling */
  .progress-container {
    width: 100%;
    background-color: #e5e7eb; /* Tailwind gray-200 */
    border-radius: 9999px;
    height: 0.5rem;
  }
  .progress-bar {
    height: 100%;
    background-color: #4f46e5; /* Tailwind indigo-600 */
    border-radius: 9999px;
    transition: width 0.3s ease;
  }
  /* Toggle password button styling */
  .field-container {
  position: relative;
  }
  .toggle-password {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #6b7280;
  }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-0">
  <div class="max-w-6xl mx-auto bg-white p-12 rounded-lg shadow-lg mt-0" x-data="tabForm()" x-init="init()">
    <!-- Back Button -->
    <div class="mb-6">
      <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Users
      </a>
    </div>
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Create New User</h2>
    
    <!-- Tab Navigation with Sliding Underline -->
    <div class="relative overflow-hidden">
      <nav class="flex space-x-8 border-b">
        <button type="button" 
                x-ref="personalBtn" 
                @click="setTab('personal')" 
                :class="activeTab === 'personal' ? 'text-indigo-600' : 'text-gray-500'" 
                class="py-4 focus:outline-none">
          Personal Information
        </button>
        <button type="button" 
                x-ref="orgBtn" 
                @click="setTab('org')" 
                :class="activeTab === 'org' ? 'text-indigo-600' : 'text-gray-500'" 
                class="py-4 focus:outline-none">
          Organizational Details
        </button>
        <button type="button" 
                x-ref="credentialsBtn" 
                @click="setTab('credentials')" 
                :class="activeTab === 'credentials' ? 'text-indigo-600' : 'text-gray-500'" 
                class="py-4 focus:outline-none">
          Login Credentials
        </button>
      </nav>
      <!-- Sliding Underline -->
      <div class="absolute bottom-0 left-0 h-1 bg-indigo-600 transition-all duration-300" 
           :style="{ width: `${indicatorWidth}px`, transform: `translateX(${indicatorLeft}px)` }"></div>
    </div>
    
    <!-- Enhanced Progress Indicator -->
    <div class="mt-4">
      <div class="text-center mb-2" x-text="`Step ${tabs.indexOf(activeTab)+1} of ${tabs.length}`"></div>
      <div class="progress-container">
        <div class="progress-bar" 
             :style="{ width: ((tabs.indexOf(activeTab) + 1) / tabs.length * 100) + '%' }">
        </div>
      </div>
    </div>
    
    <!-- Form Content Container -->
    <form id="userForm" 
          novalidate 
          action="{{ route('users.store') }}" 
          method="POST" 
          @submit.prevent="onSubmit" 
          class="mt-8 relative overflow-hidden tab-content-container" 
          style="min-height: 300px;">
      @csrf
      
      <!-- Personal Information Tab -->
      <div data-tab="personal" x-show="activeTab === 'personal' && showContent" x-cloak
           x-transition:enter="transition ease-out duration-300 delay-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-300 delay-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="absolute inset-0 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
          <div class="field-container">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   required>
          </div>
          <div class="field-container">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   required>
          </div>
          <div class="md:col-span-2 field-container">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   required>
          </div>
        </div>
        <!-- Navigation: Next Button -->
        <div class="flex justify-end items-center mt-8">
          <button type="button" @click="setTab('org')" class="flex items-center bg-blue-600 text-white font-bold px-4 py-2 rounded shadow hover:bg-blue-700 transition duration-300">
            <span>Next</span>
            <i class="fa fa-long-arrow-right text-xl ml-2" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      
      <!-- Organizational Details Tab -->
      <div data-tab="org" x-show="activeTab === 'org' && showContent" x-cloak
           x-transition:enter="transition ease-out duration-300 delay-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-300 delay-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="absolute inset-0 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
          <div class="field-container">
            <label for="division_id" class="block text-sm font-medium text-gray-700">Division</label>
            <select name="division_id" id="division_id"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required>
              <option value="">Select Division</option>
              @foreach($divisions as $division)
                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                  {{ $division->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="field-container">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" id="role"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required>
              <option value="">Select Role</option>
              <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
              <option value="Super Admin" {{ old('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
          </div>
        </div>
        <!-- Navigation: Previous & Next Buttons -->
        <div class="flex justify-between items-center mt-8">
          <button type="button" @click="setTab('personal')" class="flex items-center bg-gray-600 text-white font-bold px-4 py-2 rounded shadow hover:bg-gray-700 transition duration-300">
            <i class="fa fa-long-arrow-left text-xl mr-2" aria-hidden="true"></i>
            <span>Previous</span>
          </button>
          <button type="button" @click="setTab('credentials')" class="flex items-center bg-blue-600 text-white font-bold px-4 py-2 rounded shadow hover:bg-blue-700 transition duration-300">
            <span>Next</span>
            <i class="fa fa-long-arrow-right text-xl ml-2" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      
      <!-- Login Credentials Tab -->
      <div data-tab="credentials" x-show="activeTab === 'credentials' && showContent" x-cloak
           x-transition:enter="transition ease-out duration-300 delay-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-300 delay-200"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="absolute inset-0 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 p-4">
          <div class="field-container">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="relative">
              <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                     class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                     required>
              <button type="button" class="toggle-password absolute right-2 top-1/2 transform -translate-y-1/2" @click="showPassword = !showPassword">
                <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
          </div>
          <div class="field-container">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <div class="relative">
              <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                     class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                     required>
              <button type="button" class="toggle-password absolute right-2 top-1/2 transform -translate-y-1/2" @click="showConfirmPassword = !showConfirmPassword">
                <i :class="showConfirmPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
          </div>
        </div>
        <!-- Navigation: Previous & Create User Buttons -->
        <div class="flex justify-between items-center mt-8">
          <button type="button" @click="setTab('org')" class="flex items-center bg-gray-600 text-white font-bold px-4 py-2 rounded shadow hover:bg-gray-700 transition duration-300">
            <i class="fa fa-long-arrow-left text-xl mr-2" aria-hidden="true"></i>
            <span>Previous</span>
          </button>
          <button type="submit" class="flex items-center bg-gradient-to-r from-green-500 to-green-600 text-white font-bold px-4 py-2 rounded-lg shadow-lg hover:from-green-600 hover:to-green-700 transition duration-300">
            <span>Create User</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function tabForm() {
  return {
    activeTab: 'personal',
    showContent: true,
    tabs: ['personal', 'org', 'credentials'],
    indicatorWidth: 0,
    indicatorLeft: 0,
    showPassword: false,
    showConfirmPassword: false,

    init() {
      this.setIndicator(this.$refs.personalBtn);
      // Attach event listeners to inputs/selects within .field-container.
      document.querySelectorAll('.field-container input, .field-container select').forEach(el => {
        el.addEventListener('click', () => {
          // Clicking an element in error state shows red focus ring.
        });
        el.addEventListener('input', () => {
          // Remove error styling and message if field is corrected.
          if (el.value.trim() !== "") {
            el.classList.remove('error-active', 'border-red-500');
            let errorEl = el.parentNode.querySelector('.error-message');
            if (errorEl) errorEl.remove();
            el.removeAttribute('aria-invalid');
            el.removeAttribute('aria-describedby');
          }
        });
        // For the email field, check format on blur.
        if (el.id === 'email') {
          el.addEventListener('blur', () => {
            if (el.value.trim() !== "" && !this.isValidEmail(el.value.trim())) {
              this.triggerFieldError(el, "Please enter a valid email address.");
            }
          });
        }
      });
    },

    // Intercept final form submission.
    onSubmit() {
      if (!this.validateCurrentTab(this.activeTab)) {
        return;
      }
      document.getElementById('userForm').submit();
    },

    setIndicator(btn) {
      if (!btn) return;
      const containerLeft = btn.parentNode.getBoundingClientRect().left;
      const btnLeft = btn.getBoundingClientRect().left;
      this.indicatorWidth = btn.offsetWidth;
      this.indicatorLeft = btnLeft - containerLeft;
    },

    // Helper to add error styling and inject a persistent error message element below the field.
    triggerFieldError(el, message) {
      // Find the outer field container
      let container = el.closest('.field-container');
      // Look for an error element inside the container (but outside the relative wrapper)
      let errorEl = container.querySelector('.error-message');
      if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.className = 'error-message';
        // Append the error message at the end of the field container
        container.appendChild(errorEl);
      }
      errorEl.innerHTML = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> ' + message;
      
      // Add red border and error-active class to the input.
      el.classList.add('border-red-500', 'error-active');
      el.setAttribute('aria-invalid', 'true');
      // Optionally set aria-describedby if needed.
      
      // Scroll into view then focus the element.
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => {
        el.focus();
      }, 500);
    },

    // Basic regex check for valid email format.
    isValidEmail(emailValue) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(emailValue);
    },

    // Enhanced password format validation.
    // Password must be at least 8 characters long, contain at least one uppercase letter,
    // one lowercase letter, one digit, and one special character from the set @$!%*?&.
    isValidPassword(passwordValue) {
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
      return passwordRegex.test(passwordValue);
    },

    // Validate the fields of the current tab.
    validateCurrentTab(tabName) {
      if (tabName === 'personal') {
        const username = document.getElementById('username');
        const name = document.getElementById('name');
        const email = document.getElementById('email');

        if (!username.value.trim()) {
          this.triggerFieldError(username, "Please fill in your username.");
          return false;
        }
        if (!name.value.trim()) {
          this.triggerFieldError(name, "Please fill in your name.");
          return false;
        }
        if (!email.value.trim()) {
          this.triggerFieldError(email, "Please fill in your email.");
          return false;
        }
        if (!this.isValidEmail(email.value.trim())) {
          this.triggerFieldError(email, "Please enter a valid email address.");
          return false;
        }
      }

      if (tabName === 'org') {
        const division = document.getElementById('division_id');
        const role = document.getElementById('role');
        if (!division.value.trim()) {
          this.triggerFieldError(division, "Please select a division.");
          return false;
        }
        if (!role.value.trim()) {
          this.triggerFieldError(role, "Please select a role.");
          return false;
        }
      }

      if (tabName === 'credentials') {
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        if (!password.value) {
          this.triggerFieldError(password, "Please enter your password.");
          return false;
        }
        if (!this.isValidPassword(password.value)) {
          this.triggerFieldError(password, "Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, one digit, and one special character (allowed: @, $, !, %, *, ?, &). For example, 'Password1!'");
          return false;
        }
        if (!passwordConfirmation.value) {
          this.triggerFieldError(passwordConfirmation, "Please confirm your password.");
          return false;
        }
        if (password.value !== passwordConfirmation.value) {
          this.triggerFieldError(passwordConfirmation, "Passwords do not match.");
          return false;
        }
      }
      return true;
    },

    setTab(newTab) {
      const currentIndex = this.tabs.indexOf(this.activeTab);
      const newIndex = this.tabs.indexOf(newTab);
      if (newIndex > currentIndex && !this.validateCurrentTab(this.activeTab)) {
        return;
      }
      this.showContent = false;
      setTimeout(() => {
        this.activeTab = newTab;
        this.showContent = true;
        this.$nextTick(() => {
          const btn = this.$refs[newTab + 'Btn'];
          this.setIndicator(btn);
        });
      }, 300);
    }
  }
}
</script>
@endsection

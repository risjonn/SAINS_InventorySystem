@extends('layouts.layout')

@section('title', 'Create New User')

@section('styles')
<style>
  [x-cloak] { display: none !important; }
  .progress-container {
    width: 100%;
    background-color: #e5e7eb;
    border-radius: 9999px;
    height: 0.5rem;
  }
  .progress-bar {
    height: 100%;
    background-color: #4f46e5;
    border-radius: 9999px;
    transition: width 0.3s ease;
  }
  .password-rules {
    background-color: #f3f4f6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1rem;
  }
  .error-message {
    @apply text-red-600 text-sm mt-1;
  }
  .error-field {
    @apply border-red-500 ring-red-500 focus:ring-red-500;
  }
  .validation-list {
    @apply space-y-1 mt-2 text-sm;
  }
  .validation-item i {
    @apply mr-1;
  }
  .validation-item.valid {
    @apply text-green-600;
  }
  .validation-item.valid i {
    @apply text-green-500;
  }
  .validation-item.invalid {
    @apply text-red-600;
  }
  .validation-item.invalid i {
    @apply text-red-500;
  }
  .modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(5px);
    z-index: 9998;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .modal-container {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%) scale(0.95);
    z-index: 9999;
    background-color: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    width: 90%;
    max-width: 32rem;
    opacity: 0;
    transition: all 0.3s ease;
    border: 1px solid rgba(229, 231, 235, 0.5);
  }
  .modal-active {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
  }
  body.modal-open {
    overflow: hidden;
  }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-0">
  <!-- Confirmation Modal -->
  <div id="confirmationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="modal-overlay"></div>
    <div class="modal-container bg-white rounded-xl shadow-2xl">
      <div class="text-center p-6">
        <div class="mx-auto mb-5 h-14 w-14 bg-blue-100 rounded-full flex items-center justify-center">
          <i class="fas fa-user-check text-blue-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-5">Confirm User Details</h3>
        <div class="space-y-3 text-left mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
          <div class="flex items-center py-1">
            <span class="w-24 font-medium text-gray-500">Name:</span>
            <span class="flex-1 font-semibold text-gray-800" id="confirmName"></span>
          </div>
          <div class="flex items-center py-1">
            <span class="w-24 font-medium text-gray-500">Username:</span>
            <span class="flex-1 font-semibold text-gray-800" id="confirmUsername"></span>
          </div>
          <div class="flex items-center py-1">
            <span class="w-24 font-medium text-gray-500">Role:</span>
            <span class="flex-1 font-semibold text-gray-800" id="confirmRole"></span>
          </div>
          <div class="flex items-center py-1">
            <span class="w-24 font-medium text-gray-500">Division:</span>
            <span class="flex-1 font-semibold text-gray-800" id="confirmDivision"></span>
          </div>
          <div class="flex items-center py-1">
            <span class="w-24 font-medium text-gray-500">Access To:</span>
            <span class="flex-1 font-semibold text-gray-800" id="confirmAccess"></span>
          </div>
          <div class="flex items-center py-1">
            <span class="w-24 font-medium text-gray-500">Password:</span>
            <span class="flex-1 font-semibold text-gray-800" id="confirmPasswordType"></span>
          </div>
        </div>
        <div class="flex justify-center gap-3 mt-6">
          <button onclick="hideConfirmation()" class="px-5 py-2.5 text-gray-700 bg-gray-100 rounded-lg border border-gray-200 hover:bg-gray-200 font-medium">
            Edit
          </button>
          <button id="confirmCreate" class="px-5 py-2.5 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium shadow-md">
            Confirm & Create
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="modal-overlay"></div>
    <div class="modal-container bg-white rounded-xl shadow-2xl">
      <div class="text-center p-6">
        <div class="mx-auto mb-5 h-14 w-14 bg-green-100 rounded-full flex items-center justify-center">
          <i class="fas fa-check-circle text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-5">User Created Successfully!</h3>
        <div class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-100">
          <div class="flex items-center mb-3">
            <span class="w-20 text-gray-500 font-medium">Username:</span>
            <span class="font-mono text-gray-800 font-semibold" id="successUsername"></span>
          </div>
          <div class="flex items-center">
            <span class="w-20 text-gray-500 font-medium">Password:</span>
            <span class="font-mono text-gray-800 font-semibold" id="successPassword"></span>
          </div>
        </div>
        <button onclick="copyCredentials()"
                class="w-full mb-4 px-4 py-3 text-blue-700 font-medium bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center">
          <i class="far fa-copy mr-2"></i>Copy Credentials
        </button>
        <button onclick="closeSuccessModal()"
                class="w-full px-4 py-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium shadow-md">
          Close
        </button>
      </div>
    </div>
  </div>

  <div id="userFormRoot" class="max-w-6xl mx-auto bg-white p-12 rounded-lg shadow-lg mt-0" x-data="tabForm()" x-init="init()">
    <div class="mb-6">
      <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Back to Users
      </a>
    </div>
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Create New User</h2>
    
    <!-- Tab Navigation -->
    <div class="relative overflow-hidden">
      <nav class="flex space-x-8 border-b">
        <button type="button"
                @click="setTab('personal')"
                :class="activeTab === 'personal' ? 'text-indigo-600' : 'text-gray-500'"
                class="py-4 focus:outline-none">
          Personal Information
        </button>
        <button type="button"
                @click="setTab('login')"
                :class="activeTab === 'login' ? 'text-indigo-600' : 'text-gray-500'"
                class="py-4 focus:outline-none">
          Login Settings
        </button>
      </nav>
      <div class="absolute bottom-0 left-0 h-1 bg-indigo-600 transition-all duration-300"
           :style="{ width: `${indicatorWidth}px`, transform: `translateX(${indicatorLeft}px)` }"></div>
    </div>

    <!-- Progress Bar -->
    <div class="mt-4">
      <div class="text-center mb-2" x-text="`Step ${tabs.indexOf(activeTab)+1} of ${tabs.length}`"></div>
      <div class="progress-container">
        <div class="progress-bar"
             :style="{ width: ((tabs.indexOf(activeTab) + 1) / tabs.length * 100) + '%' }">
        </div>
      </div>
    </div>

    <!-- Form -->
    <form id="userForm" action="{{ route('users.store') }}" method="POST" class="mt-8 relative">
      @csrf
      
      <!-- Personal Information Tab -->
      <div x-show="activeTab === 'personal'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">

          <div class="field-container">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name"
                   x-model="personalData.name"
                   @blur="touched.name = true; validateField('name')"
                   @input="if (touched.name) validateField('name')"
                   :class="errors.name ? 'error-field' : ''"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
            <div x-show="errors.name" class="error-message" x-text="errors.name"></div>
          </div>
          
          <div class="field-container">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username"
                   x-model="personalData.username"
                   @blur="touched.username = true; validateField('username')"
                   @input="if (touched.username) validateField('username')"
                   :class="errors.username ? 'error-field' : ''"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
            <div x-show="errors.username" class="error-message" x-text="errors.username"></div>
          </div>

          <div class="field-container">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" id="role"
                    x-model="personalData.role"
                    @change="validateField('role')"
                    @blur="touched.role = true"
                    :class="errors.role ? 'error-field' : ''"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
              <option value="">Select Role</option>
              <option value="Admin">Admin</option>
              <option value="Super Admin">Super Admin</option>
            </select>
            <div x-show="errors.role" class="error-message" x-text="errors.role"></div>
          </div>

          <div class="field-container">
            <label for="division_id" class="block text-sm font-medium text-gray-700">Division</label>
            <select name="division_id" id="division_id"
                    x-model="personalData.division"
                    @change="validateField('division')"
                    @blur="touched.division = true"
                    :disabled="isSuperAdmin"
                    :class="errors.division ? 'error-field' : ''"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
              <option value="">Loading divisions…</option>
            </select>
            <div x-show="errors.division" class="error-message" x-text="errors.division"></div>
          </div>

          <div class="md:col-span-2">
            <p class="text-sm font-medium">
              Access To:
              <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-md" x-text="accessTo"></span>
            </p>
          </div>
        </div>

        <div class="flex justify-end">
          <button type="button"
                  @click="validateAndNext()"
                  class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Next <i class="fas fa-arrow-right ml-2"></i>
          </button>
        </div>
      </div>

      <!-- Login Settings Tab -->
      <div x-show="activeTab === 'login'" x-cloak class="space-y-6">
        <div class="p-4 space-y-6">  
          <div>
            <label class="inline-flex items-center">
              <input type="radio" name="password_option" value="auto" checked  
                     @change="showCustomPassword = false" class="form-radio">
              <span class="ml-2">Generate Temporary Password</span>
            </label>
          </div>
          <div>
            <label class="inline-flex items-center">
              <input type="radio" name="password_option" value="manual"  
                     @change="showCustomPassword = true" class="form-radio">
              <span class="ml-2">Create Custom Password</span>
            </label>
          </div>

          <div x-show="showCustomPassword" class="space-y-4">
            <div class="password-rules">
              <p class="text-sm font-medium mb-2">Password Requirements:</p>
              <ul class="validation-list">
                <li class="flex items-center space-x-2 text-sm" :class="passwordValid.minLength ? 'text-green-600' : 'text-red-600'">
                  <i class="fas" :class="passwordValid.minLength ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'"></i>
                  <span>Minimum 8 characters</span>
                </li>
                <li class="flex items-center space-x-2 text-sm" :class="passwordValid.hasUppercase ? 'text-green-600' : 'text-red-600'">
                  <i class="fas" :class="passwordValid.hasUppercase ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'"></i>
                  <span>At least one uppercase letter</span>
                </li>
                <li class="flex items-center space-x-2 text-sm" :class="passwordValid.hasLowercase ? 'text-green-600' : 'text-red-600'">
                  <i class="fas" :class="passwordValid.hasLowercase ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'"></i>
                  <span>At least one lowercase letter</span>
                </li>
                <li class="flex items-center space-x-2 text-sm" :class="passwordValid.hasNumber ? 'text-green-600' : 'text-red-600'">
                  <i class="fas" :class="passwordValid.hasNumber ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'"></i>
                  <span>At least one number</span>
                </li>
                <li class="flex items-center space-x-2 text-sm" :class="passwordValid.hasSpecial ? 'text-green-600' : 'text-red-600'">
                  <i class="fas" :class="passwordValid.hasSpecial ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'"></i>
                  <span>At least one special character (@$!%*?&)</span>
                </li>
              </ul>
            </div>
            <div class="field-container">
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <input type="password" name="password" id="password"
                     x-model="loginData.password"
                     @input="validatePasswordStrength"
                     :class="passwordError ? 'error-field' : ''"
                     class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
              <div x-show="passwordError" class="error-message" x-text="passwordError"></div>
            </div>
            <div class="field-container">
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
              <input type="password" name="password_confirmation" id="password_confirmation"
                     x-model="loginData.passwordConfirmation"
                     @input="validatePasswordConfirmation"
                     :class="passwordConfirmationError ? 'error-field' : ''"
                     class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
              <div x-show="passwordConfirmationError" class="error-message" x-text="passwordConfirmationError"></div>
            </div>
          </div>
        </div>
        <div class="flex justify-between">
          <button type="button" @click="setTab('personal')" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back
          </button>
          <button type="button" onclick="showConfirmation()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Create User
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function tabForm() {
  return {
    activeTab: 'personal',
    tabs: ['personal', 'login'],
    indicatorWidth: 0,
    indicatorLeft: 0,
    showCustomPassword: false,
    isSuperAdmin: false,
    accessTo: '',
    personalData: {
      name: '',
      username: '',
      role: '',
      division: ''
    },
    loginData: {
      password: '',
      passwordConfirmation: ''
    },
    errors: {
      name: '',
      username: '',
      role: '',
      division: ''
    },
    touched: {
      name: false,
      username: false,
      role: false,
      division: false,
      password: false,
      passwordConfirmation: false
    },
    passwordValid: {
      minLength: false,
      hasUppercase: false,
      hasLowercase: false,
      hasNumber: false,
      hasSpecial: false
    },
    passwordError: '',
    passwordConfirmationError: '',

    init() {
      this.updateAccessTo();
      this.loadDivisions();
    },

    loadDivisions() {
      fetch('{{ route("api.divisions.active") }}')
        .then(response => response.json())
        .then(data => {
          const select = document.getElementById('division_id');
          select.innerHTML = '<option value="">Select Division</option>';
          data.forEach(div => {
            select.innerHTML += `<option value="${div.id}">${div.name}</option>`;
          });
        });
    },

    validateField(field) {
      if (!this.touched[field] && field !== 'role' && field !== 'division') return;
      
      switch(field) {
        case 'name':
          this.errors.name = this.personalData.name ? '' : 'Name is required';
          break;
        case 'username':
          this.errors.username = this.personalData.username ? '' : 'Username is required';
          break;
        case 'role':
          this.errors.role = this.personalData.role ? '' : 'Role is required';
          this.updateAccessTo();
          break;
        case 'division':
          this.errors.division = (!this.isSuperAdmin && !this.personalData.division) ? 'Division is required' : '';
          this.updateAccessTo();
          break;
      }
    },

    validateAndNext() {
      // Mark all fields as touched to show errors
      this.touched.name = true;
      this.touched.username = true;
      this.touched.role = true;
      this.touched.division = true;
      
      this.validateField('name');
      this.validateField('username');
      this.validateField('role');
      this.validateField('division');

      if (Object.values(this.errors).some(error => error !== '')) {
        return;
      }
      
      this.setTab('login');
    },

    validatePasswordStrength() {
      const password = this.loginData.password;
      this.passwordValid = {
        minLength: password.length >= 8,
        hasUppercase: /[A-Z]/.test(password),
        hasLowercase: /[a-z]/.test(password),
        hasNumber: /\d/.test(password),
        hasSpecial: /[@$!%*?&]/.test(password)
      };
      
      // Use setTimeout to ensure DOM is updated
      setTimeout(() => {
        Object.keys(this.passwordValid).forEach(key => {
          const isValid = this.passwordValid[key];
          const item = document.querySelector(`.validation-item.${key}`);
          if (item) {
            if (isValid) {
              item.classList.add('valid');
              item.classList.remove('invalid');
              const icon = item.querySelector('i');
              if (icon) {
                icon.classList.add('fa-check-circle');
                icon.classList.remove('fa-times-circle');
              }
            } else {
              item.classList.add('invalid');
              item.classList.remove('valid');
              const icon = item.querySelector('i');
              if (icon) {
                icon.classList.add('fa-times-circle');
                icon.classList.remove('fa-check-circle');
              }
            }
          }
        });
      }, 0);
      
      this.passwordError = Object.values(this.passwordValid).every(v => v) 
        ? '' 
        : 'Password does not meet all requirements';
    },

    validatePasswordConfirmation() {
      this.passwordConfirmationError = 
        this.loginData.password !== this.loginData.passwordConfirmation
          ? 'Passwords do not match'
          : '';
    },

    updateAccessTo() {
      const role = this.personalData.role;
      this.isSuperAdmin = role === 'Super Admin';
      const division = document.getElementById('division_id');
      this.accessTo = this.isSuperAdmin 
        ? 'All Divisions' 
        : (division.options[division.selectedIndex]?.text || 'Please select division');
    },

    setTab(newTab) {
      this.activeTab = newTab;
      this.$nextTick(() => this.updateAccessTo());
    }
  }
}


function showConfirmation() {
  const form = document.getElementById('userForm');
  const option = document.querySelector('input[name="password_option"]:checked').value;
  if (option === 'manual') {
    const alpineData = Alpine.$data(document.getElementById('userFormRoot'));
    const allValid = Object.values(alpineData.passwordValid).every(v => v);
    const match    = alpineData.loginData.password === alpineData.loginData.passwordConfirmation;
    if (!allValid || !match) {
      alert('Please fix password errors before submitting');
      return;
    }
  }
  // populate confirmation fields
  document.getElementById('confirmName').textContent = form.name.value;
  document.getElementById('confirmUsername').textContent = form.username.value;
  document.getElementById('confirmRole').textContent = form.role.value;
  const divSelect = document.getElementById('division_id');
  document.getElementById('confirmDivision').textContent = divSelect.options[divSelect.selectedIndex]?.text;
  document.getElementById('confirmAccess').textContent = form.role.value === 'Super Admin' ? 'All Divisions' : divSelect.options[divSelect.selectedIndex]?.text;
  document.getElementById('confirmPasswordType').textContent = option === 'auto' ? 'Generated (Temporary)' : 'Custom';

  document.body.classList.add('modal-open');
  const modal = document.getElementById('confirmationModal');
  modal.classList.remove('hidden');
  
  // Force reflow to ensure transition works
  window.getComputedStyle(modal.querySelector('.modal-overlay')).opacity;
  
  // Apply transition
  setTimeout(() => {
    modal.querySelector('.modal-overlay').style.opacity = '1';
    modal.querySelector('.modal-container').classList.add('modal-active');
  }, 10);
}

function hideConfirmation() {
  const modal = document.getElementById('confirmationModal');
  modal.querySelector('.modal-overlay').style.opacity = '0';
  modal.querySelector('.modal-container').classList.remove('modal-active');
  
  setTimeout(() => {
    modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
  }, 300); // Match the duration of the transition
}

document.getElementById('confirmCreate').addEventListener('click', function(e) {
  e.preventDefault();
  const form = document.getElementById('userForm');
  const passwordOption = document.querySelector('input[name="password_option"]:checked').value;
  let finalPassword = '';

  if (passwordOption === 'auto') {
    // Generate secure password
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@$!%*?&';
    finalPassword = Array.from({ length: 12 }, () => 
      chars[Math.floor(Math.random() * chars.length)]
    ).join('');

    const hiddenPass = document.createElement('input');
    hiddenPass.type = 'hidden';
    hiddenPass.name = 'password';
    hiddenPass.value = finalPassword;
    form.appendChild(hiddenPass);

    const hiddenConfirm = document.createElement('input');
    hiddenConfirm.type = 'hidden';
    hiddenConfirm.name = 'password_confirmation';
    hiddenConfirm.value = finalPassword;
    form.appendChild(hiddenConfirm);
  } else {
    finalPassword = form.password.value;
  }

  // Submit form
  window.generatedPassword = finalPassword;

  const formData = new FormData(form);
  
  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      hideConfirmation();
      document.getElementById('successUsername').textContent = data.user.username;
      document.getElementById('successPassword').textContent = window.generatedPassword;
          
      document.body.classList.add('modal-open');
      const successModal = document.getElementById('successModal');
      successModal.classList.remove('hidden');
      
      // Force reflow to ensure transition works
      window.getComputedStyle(successModal.querySelector('.modal-overlay')).opacity;
      
      // Apply transition
      setTimeout(() => {
        successModal.querySelector('.modal-overlay').style.opacity = '1';
        successModal.querySelector('.modal-container').classList.add('modal-active');
      }, 10);
    } else {
      alert('Error: ' + (data.message || 'Failed to create user'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again.');
  });
});

function copyCredentials() {
  const username = document.getElementById('successUsername').textContent;
  const password = document.getElementById('successPassword').textContent;
  navigator.clipboard.writeText(`Username: ${username}\nPassword: ${password}`);
  
  const btn = document.querySelector('#successModal [onclick="copyCredentials()"]');
  btn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
  setTimeout(() => {
    btn.innerHTML = '<i class="far fa-copy mr-2"></i>Copy Credentials';
  }, 2000);
}

function closeSuccessModal() {
  const modal = document.getElementById('successModal');
  modal.querySelector('.modal-overlay').style.opacity = '0';
  modal.querySelector('.modal-container').classList.remove('modal-active');
  
  setTimeout(() => {
    modal.classList.add('hidden');
    document.body.classList.remove('modal-open');
    window.location.href = '{{ route('users.index') }}';
  }, 300);
}

document.getElementById('userForm').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') e.preventDefault();
});
</script>
@endsection
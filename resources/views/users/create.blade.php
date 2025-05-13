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
  #confirmationModal, #successModal {
    z-index: 50;
  }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-0">
  <!-- Confirmation Modal -->
  <div id="confirmationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <h3 class="text-lg font-medium mb-4">Confirm User Details</h3>
        <div class="text-left mb-4">
          <p><strong>Name:</strong> <span id="confirmName"></span></p>
          <p><strong>Username:</strong> <span id="confirmUsername"></span></p>
          <p><strong>Role:</strong> <span id="confirmRole"></span></p>
          <p><strong>Division:</strong> <span id="confirmDivision"></span></p>
          <p><strong>Access To:</strong> <span id="confirmAccess"></span></p>
        </div>
        <div class="flex justify-center gap-4">
          <button onclick="document.getElementById('confirmationModal').classList.add('hidden')" 
                  class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Edit
          </button>
          <button id="confirmCreate" 
                  class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Confirm
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3 text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
          <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium mt-2 mb-4">User Created Successfully!</h3>
        <div class="text-left mb-4">
          <p><strong>Username:</strong> <span id="successUsername"></span></p>
          <p><strong>Password:</strong> <span id="successPassword"></span></p>
          <button onclick="copyCredentials()" class="mt-2 text-blue-600 hover:text-blue-700">
            Copy Credentials
          </button>
        </div>
        <button onclick="window.location.href='{{ route('users.index') }}'" 
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Close
        </button>
      </div>
    </div>
  </div>

  <div class="max-w-6xl mx-auto bg-white p-12 rounded-lg shadow-lg mt-0" x-data="tabForm()" x-init="init()">
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
    <form id="userForm" action="{{ route('users.store') }}" method="POST" class="mt-8 relative overflow-hidden">
      @csrf
      
      <!-- Personal Information Tab -->
      <div x-show="activeTab === 'personal'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
          <div class="field-container">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" required
                   class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
          </div>
          <div class="field-container">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" required
                   class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
          </div>
          <div class="field-container">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" id="role" required @change="updateAccessTo()"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
              <option value="">Select Role</option>
              <option value="Admin">Admin</option>
              <option value="Super Admin">Super Admin</option>
            </select>
          </div>
          <div class="field-container">
            <label for="division_id" class="block text-sm font-medium text-gray-700">Division</label>
            <select name="division_id" id="division_id" required :disabled="isSuperAdmin"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
              <option value="">Loading divisions...</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <p class="text-sm font-medium">Access To: <span x-text="accessTo" class="font-normal"></span></p>
          </div>
        </div>
        <div class="flex justify-end">
          <button type="button" @click="setTab('login')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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
              <p class="text-sm font-medium">Password Requirements:</p>
              <ul class="list-disc pl-5 text-sm">
                <li>Minimum 8 characters</li>
                <li>At least one uppercase letter</li>
                <li>At least one lowercase letter</li>
                <li>At least one number</li>
                <li>At least one special character (@$!%*?&)</li>
              </ul>
            </div>
            <div class="field-container">
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <input type="password" name="password" id="password"
                     class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
            </div>
            <div class="field-container">
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
              <input type="password" name="password_confirmation" id="password_confirmation"
                     class="mt-1 block w-full border border-gray-300 rounded-md p-3 focus:ring-indigo-500">
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
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

    init() {
      this.updateAccessTo();
      document.getElementById('division_id').addEventListener('change', () => this.updateAccessTo());
    },

    updateAccessTo() {
      const role = document.getElementById('role').value;
      this.isSuperAdmin = role === 'Super Admin';
      const division = document.getElementById('division_id');
      this.accessTo = this.isSuperAdmin ? 
        'All Divisions' : 
        (division.options[division.selectedIndex]?.text || 'Please select division');
    },

    setTab(newTab) {
      this.activeTab = newTab;
      this.$nextTick(() => this.updateAccessTo());
    }
  }
}

function validatePassword(password) {
  const minLength = password.length >= 8;
  const hasUppercase = /[A-Z]/.test(password);
  const hasLowercase = /[a-z]/.test(password);
  const hasNumber = /[0-9]/.test(password);
  const hasSpecial = /[@$!%*?&]/.test(password);
  
  return minLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
}

// Confirmation Modal Handling
function showConfirmation() {
  // Validate password if custom password is selected
  if (document.querySelector('input[name="password_option"]:checked').value === 'manual') {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (!validatePassword(password)) {
      alert('Password does not meet requirements.');
      return;
    }
    
    if (password !== confirmPassword) {
      alert('Passwords do not match.');
      return;
    }
  }

  const form = document.getElementById('userForm');
  document.getElementById('confirmName').textContent = form.name.value;
  document.getElementById('confirmUsername').textContent = form.username.value;
  document.getElementById('confirmRole').textContent = form.role.value;
  document.getElementById('confirmDivision').textContent = form.division_id.options[form.division_id.selectedIndex]?.text;
  document.getElementById('confirmAccess').textContent = form.role.value === 'Super Admin' ? 
    'All Divisions' : form.division_id.options[form.division_id.selectedIndex]?.text;
  
  document.getElementById('confirmationModal').classList.remove('hidden');
}

// Update the confirmCreate event listener to handle both password options
document.getElementById('confirmCreate').addEventListener('click', function(e) {
  e.preventDefault(); // Prevent immediate form submission
  
  const form = document.getElementById('userForm');
  const passwordOption = document.querySelector('input[name="password_option"]:checked').value;
  const username = form.username.value;
  
  // For auto-generated password
  if(passwordOption === 'auto') {
    // Generate a more secure password
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@$!%*?&';
    let tempPassword = '';
    for (let i = 0; i < 12; i++) {
      tempPassword += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    
    // Create password and confirmation fields
    let hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'password';
    hiddenInput.value = tempPassword;
    form.appendChild(hiddenInput);
    
    let hiddenConfirmInput = document.createElement('input');
    hiddenConfirmInput.type = 'hidden';
    hiddenConfirmInput.name = 'password_confirmation';
    hiddenConfirmInput.value = tempPassword;
    form.appendChild(hiddenConfirmInput);
    
    // Update success modal content
    document.getElementById('successUsername').textContent = username;
    document.getElementById('successPassword').textContent = tempPassword;
  } else {
    // For custom password, grab the entered password and show it in the success modal
    const customPassword = document.getElementById('password').value;
    document.getElementById('successUsername').textContent = username;
    document.getElementById('successPassword').textContent = customPassword;
  }
  
  // Hide confirmation modal
  document.getElementById('confirmationModal').classList.add('hidden');
  
  // Submit form using fetch to prevent page refresh
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
      // Show success modal
      document.getElementById('successModal').classList.remove('hidden');
    } else {
      alert('Error creating user: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error creating user. Please try again.');
  });
});

function copyCredentials() {
  const text = `Username: ${document.getElementById('successUsername').textContent}\nPassword: ${document.getElementById('successPassword').textContent}`;
  navigator.clipboard.writeText(text);
}

// Division Loading (Keep original division loading code)
let divisionsCache = [];
document.addEventListener('DOMContentLoaded', function() {
  fetch('{{ route("api.divisions.active") }}')
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById('division_id');
      select.innerHTML = '<option value="">Select Division</option>';
      data.forEach(div => {
        select.innerHTML += `<option value="${div.id}">${div.name}</option>`;
      });
    });
});

document.getElementById('userForm').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    return false;
  }
});

document.querySelector('#successModal button').addEventListener('click', function(e) {
  e.preventDefault();
  window.location.href = '{{ route('users.index') }}';
});
</script>
@endsection
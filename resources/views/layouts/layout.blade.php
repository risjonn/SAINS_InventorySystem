<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>@yield('title')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.store('activeTooltip', {
        current: null,
        set(id) {
          this.current = id;
        },
        clear(id) {
          if (this.current === id) {
            this.current = null;
          }
        }
      });
    });
  </script>
  @vite(['resources/css/app.css'])
</head>
<body class="bg-light" x-data="{ 
    sidebarOpen: localStorage.getItem('sidebarOpen') === 'true', 
    mobileSidebarOpen: false 
  }" 
  @resize.window="sidebarOpen = window.innerWidth >= 1024 ? sidebarOpen : false"
  x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))"
  x-cloak>
  <!-- Sidebar Backdrop (mobile only) -->
  <div x-show="mobileSidebarOpen" 
       @click="mobileSidebarOpen = false" 
       class="lg:hidden fixed inset-0 bg-black/50 z-30 transition-opacity"
       x-transition.opacity>
  </div>

  <!-- Sidebar -->
  <aside class="fixed top-0 left-0 z-40 h-screen bg-white shadow-lg border-r border-gray-100"
         :class="sidebarOpen ? 'w-64' : 'w-20'"
         style="transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1);">
    <div class="relative">
      <!-- Sidebar Header -->
      <div class="p-4 border-b border-gray-100 flex items-center justify-between h-16">
        <!-- Text Header: Visible when sidebar is open -->
        <h2 class="text-xl font-bold text-gray-700 whitespace-nowrap overflow-hidden" 
            x-show="sidebarOpen"
            :class="sidebarOpen ? 'opacity-100 max-w-full' : 'opacity-0 max-w-0'">
          Stock System
        </h2>
        <!-- Image: Visible when sidebar is closed -->
        <img 
          x-show="!sidebarOpen" 
          src="{{ asset('image/SAINS2.jpeg') }}" 
          alt="SAINS Inventory System Logo" 
          class="w-10 h-10 mx-auto opacity-100 transition-all duration-300 ease-in-out"
          loading="lazy"
          onerror="this.onerror=null; this.src='{{ asset('images/fallback.jpeg') }}';"
        >
      </div>
      <nav class="mt-4 px-2 space-y-1">
        <!-- Dashboard Link -->
        <div x-data="{
              id: 'dashboard',
              iconHovered: false,
              tooltipHovered: false,
              setActive() { $store.activeTooltip.set(this.id) },
              clearActive() { if(!this.iconHovered && !this.tooltipHovered){ $store.activeTooltip.clear(this.id) } }
            }"
            class="relative group"
            @mouseenter="iconHovered = true; setActive()"
            @mouseleave="iconHovered = false; clearActive()">
          <a href="{{ route('dashboard') }}" 
             :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
             class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-blue-600' : 'text-gray-600' }}"
             x-bind:class="($store.activeTooltip.current === 'dashboard') ? 'bg-gray-200 text-blue-600' : ''">
            <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600">
              <i class="fas fa-home text-lg"></i>
            </div>
            <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
              Dashboard
            </span>
          </a>
          <template x-if="!sidebarOpen">
            <div x-show="$store.activeTooltip.current === id" 
                 x-cloak 
                 class="absolute left-full ml-0 top-1/2 -translate-y-1/2 transition duration-150 z-50"
                 @mouseenter="tooltipHovered = true; setActive()"
                 @mouseleave="tooltipHovered = false; clearActive()">
              <div class="relative">
                <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium">
                  Dashboard
                </div>
                <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-1">
                  <svg class="w-3 h-3 fill-current text-gray-200" viewBox="0 0 10 10">
                    <polygon points="0,0 10,0 5,5"/>
                  </svg>
                </div>
              </div>
            </div>
          </template>
        </div>
        
        @if(auth()->user()->role === 'Admin')
        <!-- Stock Inventory Link -->
        <div x-data="{
                id: 'stockInventory',
                stockMenuOpen: localStorage.getItem('stockMenuOpen') === 'true',
                iconHovered: false,
                tooltipHovered: false,
                setActive() { $store.activeTooltip.set(this.id) },
                clearActive() { if(!this.iconHovered && !this.tooltipHovered){ $store.activeTooltip.clear(this.id) } }
            }"
            x-init="$watch('stockMenuOpen', value => localStorage.setItem('stockMenuOpen', value))"
            class="relative group"
            @mouseenter="iconHovered = true; setActive()"
            @mouseleave="iconHovered = false; clearActive()">
          <!-- Parent Button -->
          <button @click="if(sidebarOpen){ stockMenuOpen = !stockMenuOpen }"
                  :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
                  class="flex items-center justify-between w-full p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 {{ (request()->routeIs('stocks.overview') || request()->routeIs('stocks.byCategory') || request()->routeIs('stocks.transactions') || request()->routeIs('stocks.management') || request()->routeIs('stocks.transactions.in')) ? 'bg-gray-100 text-blue-600' : 'text-gray-600' }}"
                  x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
            <div class="flex items-center">
              <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600">
                <i class="fas fa-boxes text-lg"></i>
              </div>
              <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
                Stock Inventory
              </span>
            </div>
            <span x-show="sidebarOpen" x-cloak class="transition-transform duration-150">
              <i :class="{'fa-chevron-down': !stockMenuOpen, 'fa-chevron-up': stockMenuOpen}" class="fas"></i>
            </span>
          </button>
          <!-- Tooltip Dropdown when Sidebar is Collapsed -->
          <template x-if="!sidebarOpen">
            <div x-show="$store.activeTooltip.current === id" 
                 x-cloak 
                 class="absolute left-full ml-0 top-0 transition duration-150 z-50"
                 @mouseenter="tooltipHovered = true; setActive()"
                 @mouseleave="tooltipHovered = false; clearActive()">
              <div class="relative">
                <div class="bg-gray-200 text-gray-600 flex flex-col rounded-r-lg rounded-l-none p-3 text-sm font-medium space-y-1">
                  <a href="{{ route('stocks.overview') }}"
                     class="block h-12 flex items-center rounded hover:bg-gray-200 hover:text-blue-600 hover:scale-105 px-2 whitespace-nowrap {{ request()->routeIs('stocks.overview', 'stocks.byCategory') ? 'text-blue-600' : 'text-gray-600' }}"
                     x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
                    Stock Overview
                  </a>
                  <a href="{{ route('stocks.transactions') }}"
                     class="block h-12 flex items-center rounded hover:bg-gray-200 hover:text-blue-600 hover:scale-105 px-2 whitespace-nowrap {{ request()->routeIs('stocks.transactions') ? 'text-blue-600' : 'text-gray-600' }}"
                     x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
                    Stock Transaction
                  </a>
                  <a href="{{ route('stocks.management') }}"
                     class="block h-12 flex items-center rounded hover:bg-gray-200 hover:text-blue-600 hover:scale-105 px-2 whitespace-nowrap {{ request()->routeIs('stocks.management') ? 'text-blue-600' : 'text-gray-600' }}"
                     x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
                    Stock Management
                  </a>
                </div>
                <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-1">
                  <svg class="w-3 h-3 fill-current text-gray-200" viewBox="0 0 10 10">
                    <polygon points="0,0 10,0 5,5"/>
                  </svg>
                </div>
              </div>
            </div>
          </template>
          <!-- Inline Submenu when Sidebar is Expanded -->
          <div x-show="sidebarOpen && stockMenuOpen" x-cloak class="ml-8 mt-2 space-y-1">
            <a href="{{ route('stocks.overview') }}"
               class="block p-2 rounded-lg hover:bg-gray-200 hover:text-blue-600 transition-colors transform hover:scale-105 duration-150 whitespace-nowrap {{ request()->routeIs('stocks.overview', 'stocks.byCategory') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
              Stock Overview
            </a>
            <a href="{{ route('stocks.transactions') }}"
               class="block p-2 rounded-lg hover:bg-gray-200 hover:text-blue-600 transition-colors transform hover:scale-105 duration-150 whitespace-nowrap {{ request()->routeIs('stocks.transactions') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
              Stock Transaction
            </a>
            <a href="{{ route('stocks.management') }}"
               class="block p-2 rounded-lg hover:bg-gray-200 hover:text-blue-600 transition-colors transform hover:scale-105 duration-150 whitespace-nowrap {{ request()->routeIs('stocks.management') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
              Stock Management
            </a>
          </div>
        </div>
        @endif
        
        <!-- User Manager-only Link -->
        @if(auth()->user()->role === 'User Manager')
        <div x-data="{ id: 'userManager' }"
             @mouseenter="$store.activeTooltip.set(id)"
             @mouseleave="setTimeout(() => { $store.activeTooltip.clear(id) }, 0)"
             class="relative group">
          <a href="{{ route('users.index') }}" 
             :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
             class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 text-gray-600"
             x-bind:class="($store.activeTooltip.current === 'userManager') ? 'bg-gray-200 text-blue-600' : ''">
            <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600">
              <i class="fas fa-users text-lg"></i>
            </div>
            <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
              Manage Users
            </span>
          </a>
          <template x-if="!sidebarOpen">
            <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2 -translate-y-1/2 transition duration-150 z-50 pointer-events-none">
              <div class="relative">
                <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium whitespace-nowrap">
                  Manage Users
                </div>
                <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-1">
                  <svg class="w-3 h-3 fill-current text-gray-200" viewBox="0 0 10 10">
                    <polygon points="0,0 10,0 5,5"/>
                  </svg>
                </div>
              </div>
            </div>
          </template>
        </div>
        @endif
        
        <!-- Profile Settings Link -->
        <div x-data="{ id: 'profileSettings' }"
             @mouseenter="$store.activeTooltip.set(id)"
             @mouseleave="setTimeout(() => { $store.activeTooltip.clear(id) }, 0)"
             class="relative group">
          <a href="{{ route('profile.edit') }}" 
             :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
             class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 {{ request()->routeIs('profile.edit') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}"
             x-bind:class="($store.activeTooltip.current === 'profileSettings') ? 'bg-gray-200 text-blue-600' : ''">
            <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600">
              <i class="fas fa-user-cog text-lg"></i>
            </div>
            <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
              Profile Settings
            </span>
          </a>
          <template x-if="!sidebarOpen">
            <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2 -translate-y-1/2 transition duration-150 z-50">
              <div class="relative">
                <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium whitespace-nowrap">
                  Profile Settings
                </div>
                <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-1">
                  <svg class="w-3 h-3 fill-current text-gray-200" viewBox="0 0 10 10">
                    <polygon points="0,0 10,0 5,5"/>
                  </svg>
                </div>
              </div>
            </div>
          </template>
        </div>
        
        <!-- Logout Link -->
        <div x-data="{ id: 'logout' }"
             @mouseenter="$store.activeTooltip.set(id)"
             @mouseleave="setTimeout(() => { $store.activeTooltip.clear(id) }, 0)"
             class="relative group">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
                    class="flex items-center w-full p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 text-gray-600"
                    x-bind:class="($store.activeTooltip.current === 'logout') ? 'bg-gray-200 text-blue-600' : ''">
              <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600">
                <i class="fas fa-sign-out-alt text-lg"></i>
              </div>
              <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
                Logout
              </span>
            </button>
          </form>
          <template x-if="!sidebarOpen">
            <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2 -translate-y-1/2 transition duration-150 z-50">
              <div class="relative">
                <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium whitespace-nowrap">
                  Logout
                </div>
                <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-1">
                  <svg class="w-3 h-3 fill-current text-gray-200" viewBox="0 0 10 10">
                    <polygon points="0,0 10,0 5,5"/>
                  </svg>
                </div>
              </div>
            </div>
          </template>
        </div>
      </nav>
      
      <!-- Edge Hover Zone -->
      <div class="absolute right-0 top-0 bottom-0 w-2 cursor-pointer hover:bg-gray-200/50"
           @click="sidebarOpen = !sidebarOpen"
           x-show="sidebarOpen">
      </div>

      <!-- Bottom Toggle -->
      <div class="mt-auto border-t border-gray-100 p-2">
        <button @click="sidebarOpen = !sidebarOpen" 
                class="w-full flex items-center justify-center p-3 text-gray-600 hover:bg-gray-100 rounded-lg gap-2">
          <i class="fas fa-chevron-left text-lg transform transition-transform"
             :class="{ 'rotate-180': !sidebarOpen }"></i>
          <span x-show="sidebarOpen" class="text-sm">Collapse Menu</span>
        </button>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="min-h-screen transition-[margin] duration-300 ease-in-out"
        :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
    <!-- Mobile Header -->
    <header class="lg:hidden bg-white shadow-sm p-4 flex items-center justify-between">
      <button @click="mobileSidebarOpen = true" class="text-gray-600">
        <i class="fas fa-bars fa-lg"></i>
      </button>
      <h1 class="text-xl font-semibold">@yield('title')</h1>
    </header>

    <!-- Content Container -->
    <div class="p-6">
      @if (session('success'))
        <div id="popup-message" class="alert alert-success alert-dismissible fade show shadow popup-center" role="alert">
          <div class="d-flex flex-column align-items-center p-4">
            @if (session('action') === 'add')
              <i class="fas fa-check-circle fa-3x text-success tick-animation mb-3"></i>
            @elseif (session('action') === 'edit')
              <i class="fas fa-edit fa-3x text-primary edit-animation mb-3"></i>
            @elseif (session('action') === 'delete')
              <i class="fas fa-trash fa-3x text-danger trashShake mb-3"></i>
            @endif
            <h4 class="mb-0 text-center">
              <strong>{{ session('success') }}</strong>
            </h4>
          </div>
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close"></button>
        </div>
        <div id="overlay" class="popup-overlay"></div>
      @endif

      @yield('content')
    </div>
  </main>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger" id="deleteModalLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this item? This action cannot be undone.
        </div>
        <div class="modal-footer">
          <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  @yield('scripts')

  <!-- Scripts -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const popupMessage = document.getElementById('popup-message');
      const overlay = document.getElementById('overlay');
      if (popupMessage) {
        overlay.style.display = 'block';
        popupMessage.querySelector('.btn-close').addEventListener('click', () => {
          popupMessage.remove();
          overlay.remove();
        });
      }

      const deleteModalElement = document.getElementById('deleteModal');
      const deleteModal = new bootstrap.Modal(deleteModalElement, {
        backdrop: 'static',
        keyboard: false
      });

      const deleteButtons = document.querySelectorAll('.delete-button');
      const deleteForm = document.getElementById('deleteForm');

      deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
          e.preventDefault();
          const action = button.getAttribute('data-action');
          deleteForm.setAttribute('action', action);
          deleteModal.show();
        });
      });
    });
  </script>
</body>
</html>

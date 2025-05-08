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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
    <!-- Include JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    @vite(['resources/css/app.css', 'resources/css/modal.css'])
    @yield('styles')
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

  @if (!request()->is('forgot-password'))
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
             class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 
                      {{ (request()->routeIs('dashboard') || request()->routeIs('stocks.low')) ? 'bg-gray-100 text-blue-600' : 'text-gray-600' }}"
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
                    <polygon points="0,0 10,0 5,5"></polygon>
                  </svg>
                </div>
              </div>
            </div>
          </template>
        </div>

          <!-- Stock Inventory Link with Overdue Alert Badge (icon badge + sublinks) -->
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
                    class="flex items-center justify-start w-full p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 
                          {{ (request()->routeIs('stocks.overview') || request()->routeIs('stocks.byCategory') || request()->routeIs('stocks.transactions') || request()->routeIs('stocks.management') || request()->routeIs('stocks.transactions.in')) ? 'bg-gray-100 text-blue-600' : 'text-gray-600' }}"
                    x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
              <!-- Icon container with a relative class so we can place a small badge in the corner -->
              <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600 relative">
                <i class="fas fa-boxes text-lg"></i>
              </div>

              <!-- Show an icon-level badge even when collapsed -->
              @if(! request()->routeIs('stocks.management') && isset($overdueCount) && $overdueCount > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    {{ $overdueCount }}
                </span>
              @endif
              <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
                Stock Inventory
              </span>
              <span x-show="sidebarOpen" x-cloak class="ml-auto transition-transform duration-150">
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
                      class="block h-12 flex items-center rounded hover:bg-gray-200 hover:text-blue-600 hover:scale-105 px-2 whitespace-nowrap 
                              {{ request()->routeIs('stocks.overview', 'stocks.byCategory') ? 'text-blue-600' : 'text-gray-600' }}"
                      x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
                      Stock Overview
                    </a>
                    <a href="{{ route('stocks.transactions') }}"
                      class="block h-12 flex items-center rounded hover:bg-gray-200 hover:text-blue-600 hover:scale-105 px-2 whitespace-nowrap 
                              {{ request()->routeIs('stocks.transactions') ? 'text-blue-600' : 'text-gray-600' }}"
                      x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
                      Stock Transaction
                    </a>
                    <a href="{{ route('stocks.management') }}"
                      class="relative block h-12 flex items-center rounded hover:bg-gray-200 hover:text-blue-600 hover:scale-105 px-2 whitespace-nowrap 
                              {{ request()->routeIs('stocks.management') ? 'text-blue-600' : 'text-gray-600' }}"
                      x-bind:class="($store.activeTooltip.current === 'stockInventory') ? 'bg-gray-200 text-blue-600' : ''">
                      Stock Management
                      <!-- Overdue Alert Badge (inline sublink badge) -->
                      @if(! request()->routeIs('stocks.management') && $overdueCount > 0)
                          <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                              {{ $overdueCount }}
                          </span>
                      @endif
                    </a>
                  </div>
                  <div class="absolute left-1/2 transform -translate-x-1/2 -bottom-1">
                    <svg class="w-3 h-3 fill-current text-gray-200" viewBox="0 0 10 10">
                      <polygon points="0,0 10,0 5,5"></polygon>
                    </svg>
                  </div>
                </div>
              </div>
            </template>
            <!-- Inline Submenu when Sidebar is Expanded -->
            <div x-show="sidebarOpen && stockMenuOpen" x-cloak class="ml-8 mt-2 space-y-1">
              <a href="{{ route('stocks.overview') }}"
                class="block p-2 rounded-lg hover:bg-gray-200 hover:text-blue-600 transition-colors transform hover:scale-105 duration-150 whitespace-nowrap 
                        {{ request()->routeIs('stocks.overview', 'stocks.byCategory') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
                Stock Overview
              </a>
              <a href="{{ route('stocks.transactions') }}"
                class="block p-2 rounded-lg hover:bg-gray-200 hover:text-blue-600 transition-colors transform hover:scale-105 duration-150 whitespace-nowrap 
                        {{ request()->routeIs('stocks.transactions') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
                Stock Transaction
              </a>
              <a href="{{ route('stocks.management') }}"
                class="block p-2 rounded-lg hover:bg-gray-200 hover:text-blue-600 transition-colors transform hover:scale-105 duration-150 whitespace-nowrap 
                        {{ request()->routeIs('stocks.management') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
                Stock Management
                @if(! request()->routeIs('stocks.management') && isset($overdueCount) && $overdueCount > 0)
                  <span class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                    {{ $overdueCount }}
                  </span>
                @endif
              </a>
            </div>
          </div>
          
          <!-- Super Admin-only Link -->
          @if(auth()->user()->role === 'Super Admin')
          <div x-data="{ id: 'userManager' }"
              @mouseenter="$store.activeTooltip.set(id)"
              @mouseleave="setTimeout(() => { $store.activeTooltip.clear(id) }, 0)"
              class="relative group">
              <a href="{{ route('users.index') }}" 
                :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
                class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 {{ request()->routeIs('users*') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}"
                x-bind:class="($store.activeTooltip.current === 'userManager') ? 'bg-gray-200 text-blue-600' : ''">
                <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600 relative">
                    <i class="fas fa-users text-lg"></i>
                </div>
                @if(isset($totalRequestsCount) && $totalRequestsCount > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $totalRequestsCount }}
                    </span>
                @endif
                <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
                    Manage Users
                </span>
                
                {{-- @if(isset($totalRequestsCount) && $totalRequestsCount > 0)
                    <span x-show="sidebarOpen" x-cloak class="ml-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $totalRequestsCount }}
                    </span>
                @endif --}}
              </a>
              <template x-if="!sidebarOpen">
                  <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2  -translate-y-1/2 transition duration-150 z-50 pointer-events-none">
                    <div class="relative">
                        <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium whitespace-nowrap">
                          Manage Users
                          {{-- @if(isset($totalRequestsCount) && $totalRequestsCount > 0)
                              <span class="ml-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                  {{ $totalRequestsCount }}
                              </span>
                          @endif --}}
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

          <!-- Products Link -->
          <div x-data="{ id: 'products' }"
              class="relative group"
              @mouseenter="$store.activeTooltip.set(id)"
              @mouseleave="setTimeout(() => { $store.activeTooltip.clear(id) }, 0)">
          <a href="{{ route('products.index') }}"
              :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
              class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 {{ request()->routeIs('products*') || request()->routeIs('category*') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}"
              x-bind:class="($store.activeTooltip.current === 'products') ? 'bg-gray-200 text-blue-600' : ''">
            <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center">
              <i class="fas fa-box text-lg"></i>
            </div>
            <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
              Products
            </span>
          </a>
          <template x-if="!sidebarOpen">
            <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2 -translate-y-1/2 transition duration-150 z-50">
              <div class="relative">
                <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium whitespace-nowrap">
                  Products
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
      @endif



    <!-- Main Content -->
    <main class="min-h-screen transition-[margin] duration-300 ease-in-out"
    :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

      <!-- Content Container -->
      <div class="p-6">
        
        @yield('content')
        
        <!-- Global Modals -->
        @include('components.success-modal')
        @include('components.delete-confirmation-modal')
        @include('components.reset-password-modal')

        {{-- Global Delete Form (for DELETE requests) --}}
        <form id="globalDeleteForm" method="POST" style="display: none;">
          @csrf
          @method('DELETE')
        </form>

        {{-- Global Status Form (for POST activate/deactivate) --}}
        <form id="globalStatusForm" method="POST" style="display: none;">
            @csrf
        </form>
        
        <!-- Custom JS to handle modals -->
        <script>
          // Success Modal Function
          function showGlobalSuccessModal(type, customMessage = '', resource = '') {
            let title, defaultMessage, headerClass, iconClass;
            
            switch (type) {
              case 'created':
                title = resource ? `New ${capitalize(resource)} Added` : 'Creation Successful!';
                defaultMessage = resource ? `The new ${resource} has been created successfully.` : 'Operation completed successfully.';
                headerClass = 'created';
                iconClass = 'fa-check-circle';
                break;
              case 'updated':
                title = resource ? `${capitalize(resource)} Updated` : 'Update Completed!';
                defaultMessage = resource ? `The ${resource} has been updated successfully.` : 'Operation completed successfully.';
                headerClass = 'updated';
                iconClass = 'fa-sync-alt';
                break;
              case 'deleted':
                title = resource ? `${capitalize(resource)} Removed` : 'Deletion Successful!';
                defaultMessage = resource ? `The ${resource} has been deleted from the system.` : 'Operation completed successfully.';
                headerClass = 'deleted';
                iconClass = 'fa-trash-alt';
                break;
              case 'activated':
                title = resource ? `${capitalize(resource)} Activated` : 'Activation Successful!';
                defaultMessage = resource ? `The ${resource} has been activated.` : 'Operation completed successfully';
                headerClass = 'activated';
                iconClass = 'fa-user-check';
                break;
              case 'deactivated':
                title = resource ? `${capitalize(resource)} Deactivated` : 'Deactivation Successful!';
                defaultMessage = resource ? `The ${resource} has been deactivated.` : 'Operation completed successfully';
                headerClass = 'deactivated';
                iconClass = 'fa-user-slash';
                break;
              case 'loan':
                title = resource ? `${capitalize(resource)} Loaned` : 'Loan Successful!';
                defaultMessage = resource ? `The ${resource} has been loaned successfully.` : 'Operation completed successfully.';
                headerClass = 'loan';
                iconClass = 'fa-hand-holding';
                break;
              case 'returned':
                title = resource ? `${capitalize(resource)} Returned` : 'Return Successful!';
                defaultMessage = resource ? `The ${resource} has been returned successfully.` : 'Operation completed successfully.';
                headerClass = 'returned';
                iconClass = 'fa-undo';
                break;
              case 'out':
                title = resource ? `${capitalize(resource)} Out` : 'OUT Operation Successful!';
                defaultMessage = resource ? `The ${resource} has been marked as OUT successfully.` : 'Operation completed successfully.';
                headerClass = 'out';
                iconClass = 'fa-arrow-right';
                break;
              default:
                title = 'Success';
                defaultMessage = 'Operation completed successfully.';
                headerClass = 'default';
                iconClass = 'fa-info-circle';
            }
            
            // Use customMessage if provided; otherwise, use defaultMessage.
            let message = customMessage || defaultMessage;
            
            $('#globalSuccessModalLabel').text(title);
            $('#globalSuccessModal .modal-header')
              .removeClass('default created updated deleted loan returned out')
              .addClass(headerClass);
            $('#successModalIcon').removeClass().addClass('fas ' + iconClass + ' success-icon');
            $('#globalSuccessModalBody').html(message);
            $('#globalSuccessModal').modal('show');
          }

          // Utility function to capitalize the first letter:
          function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
          }

          function showActionConfirmationModal(actionType, itemType, onConfirm) {
            // 1) Determine header class, title, button style, icon
            let title, headerClass, btnClass;
            switch (actionType) {
              case 'delete':
                title       = 'Confirm Delete';
                headerClass = 'delete-confirm';
                btnClass    = 'btn-danger';
                break;
              case 'activate':
                title       = 'Confirm Activation';
                headerClass = 'activate-confirm';
                btnClass    = 'btn-success';
                break;
              case 'deactivate':
                title       = 'Confirm Deactivation';
                headerClass = 'deactivate-confirm';
                btnClass    = 'btn-warning';
                break;
              default:
                title       = 'Confirm';
                headerClass = '';
                btnClass    = 'btn-primary';
            }

            // Compute the button label
            let btnText = actionType === 'delete'
                ? 'Delete'
                : actionType.charAt(0).toUpperCase() + actionType.slice(1);

            // 2) Update modal header text & styling
            $('#globalDeleteModalLabel').text(title);
            let $modalHeader = $('#globalDeleteModal .modal-header');
            $modalHeader
              .removeClass('delete-confirm activate-confirm deactivate-confirm')
              .addClass(headerClass);

            // 3) Update message
            $('#deleteConfirmationMessage').text(
              `Are you sure you want to ${actionType} this ${itemType}?`
            );

            // 4) Update confirm button text & style
            $('#confirmDeleteButton')
              .off('click')
              .text(btnText)     // e.g. 'Delete', 'Activate'
              .removeClass('btn-danger btn-success btn-warning')
              .addClass(btnClass)
              .on('click', function() {
                onConfirm();
                $('#globalDeleteModal').modal('hide');
              });

            // 5) Show the modal
            $('#globalDeleteModal').modal('show');
          }

          // When the modal is fully hidden, reset it back to delete defaults
          $('#globalDeleteModal').on('hidden.bs.modal', function() {
            const $header = $('#globalDeleteModal .modal-header');

            // Reset header text, classes
            $('#globalDeleteModalLabel').text('Confirm Delete');
            $header
              .removeClass('activate-confirm deactivate-confirm')
              .addClass('delete-confirm');

            // Reset confirm button text & classes
            $('#confirmDeleteButton')
              .text('Delete')
              .removeClass('btn-success btn-warning')
              .addClass('btn-danger');
          });

          // Deletion handler
          $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
            const action   = $(this).data('action');
            const itemType = $(this).data('type');
            showActionConfirmationModal('delete', itemType, function() {
              $('#globalDeleteForm').attr('action', action).submit();
            });
          });

          // Activate / Deactivate handler
          $(document).on('click', '.status-button', function(e) {
            e.preventDefault();
            const actionType = $(this).data('action-label');   // 'activate' or 'deactivate'
            const action     = $(this).data('action');
            const itemType   = $(this).data('type');

            showActionConfirmationModal(actionType, itemType, function() {
              $('#globalStatusForm').attr('action', action).submit();
            });
          });
        </script>

        @if(session('success'))
          <script>
            $(document).ready(function(){
              let message = {!! json_encode(session('success')) !!};
              let type = 'created'; // Default type
              let lowerMsg = message.toLowerCase();
              
              // Be more specific with the checks and use the proper order
              if(lowerMsg.indexOf('deactivated') !== -1) {
                type = 'deactivated';
              } else if(lowerMsg.indexOf('activated') !== -1) {
                type = 'activated';
              } else if(lowerMsg.indexOf('created') !== -1) {
                type = 'created';
              } else if(lowerMsg.indexOf('updated') !== -1) {
                type = 'updated';
              } else if(lowerMsg.indexOf('deleted') !== -1 || lowerMsg.indexOf('removed') !== -1) {
                type = 'deleted';
              } else if(lowerMsg.indexOf('loan') !== -1) {
                type = 'loan';
              } else if(lowerMsg.indexOf('returned') !== -1) {
                type = 'returned';
              } else if(lowerMsg.indexOf('out') !== -1) {
                type = 'out';
              }
              
              let resource = {!! json_encode(session('resource') ?? 'resource') !!};
              showGlobalSuccessModal(type, message, resource);
            });
          </script>
        @endif
      </div>
    </main>

    @yield('scripts')
    
  </body>
  </html>

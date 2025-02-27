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
                <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center group-hover:text-blue-600">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
                    Manage Users
                </span>
              </a>
                <template x-if="!sidebarOpen">
                    <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2  -translate-y-1/2 transition duration-150 z-50 pointer-events-none">
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

          <div x-data="{ id: 'brands' }"
             @mouseenter="$store.activeTooltip.set(id)"
             @mouseleave="setTimeout(() => { $store.activeTooltip.clear(id) }, 0)"
             class="relative group">
              <a href="{{ route('brands.index') }}" 
                  :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
                  class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 {{ request()->routeIs('brands*') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}"
                  x-bind:class="($store.activeTooltip.current === 'brands') ? 'bg-gray-200 text-blue-600' : ''">
                <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center">
                    <i class="fas fa-tag text-lg"></i>
                </div>
                <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
                    Brands
                </span>
              </a>
              <template x-if="!sidebarOpen">
                <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2 -translate-y-1/2 transition duration-150 z-50">
                  <div class="relative">
                    <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium whitespace-nowrap">
                      Brands
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

          <div x-data="{ id: 'category' }"
          @mouseenter="$store.activeTooltip.set(id)"
          @mouseleave="setTimeout(() => { $store.activeTooltip.clear(id) }, 0)"
          class="relative group">
           <a href="{{ route('category.index') }}" 
               :class="sidebarOpen ? 'rounded-lg' : 'rounded-l-lg rounded-r-none'"
               class="flex items-center p-3 hover:bg-gray-200 hover:text-blue-600 transition duration-150 {{ request()->routeIs('category*') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}"
               x-bind:class="($store.activeTooltip.current === 'category') ? 'bg-gray-200 text-blue-600' : ''">
             <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center">
                 <i class="fas fa-list-alt text-lg"></i>
             </div>
             <span x-show="sidebarOpen" x-cloak class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-150">
                 Category
             </span>
           </a>
           <template x-if="!sidebarOpen">
             <div x-show="$store.activeTooltip.current === id" x-cloak class="absolute left-full ml-0 top-1/2 -translate-y-1/2 transition duration-150 z-50">
               <div class="relative">
                 <div class="bg-gray-200 text-blue-600 flex items-center h-14 rounded-r-lg rounded-l-none p-4 text-sm font-medium whitespace-nowrap">
                   Category
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

    <!-- Content Container -->
    <div class="p-6">
      
      @yield('content')
      
      <!-- Global Modals -->
      @include('components.success-modal')
      @include('components.delete-confirmation-modal')
      
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
            .removeClass('default created updated deleted')
            .addClass(headerClass);
          $('#successModalIcon').removeClass().addClass('fas ' + iconClass + ' success-icon');
          $('#globalSuccessModalBody').html(message);
          $('#globalSuccessModal').modal('show');
        }
    
        // Utility function to capitalize the first letter:
        function capitalize(str) {
          return str.charAt(0).toUpperCase() + str.slice(1);
        }
        
        // Delete Confirmation Modal Function
        function showDeleteConfirmationModal(itemType, confirmCallback) {
          var message = "Are you sure you want to delete this " + itemType + "?";
          $('#deleteConfirmationMessage').html(message);
          
          $('#globalDeleteModal').modal('show');
    
          $('#confirmDeleteButton').off('click').on('click', function() {
              confirmCallback(itemType);
              $('#globalDeleteModal').modal('hide');
          });
        }
      </script>

      @if(session('success'))
        <script>
          $(document).ready(function(){
            // Get the flash success message
            let message = {!! json_encode(session('success')) !!};
            // Determine the modal type based on message content
            let type = 'created';
            let lowerMsg = message.toLowerCase();
            if(lowerMsg.indexOf('created') !== -1) {
              type = 'created';
            } else if(lowerMsg.indexOf('updated') !== -1) {
              type = 'updated';
            } else if(lowerMsg.indexOf('deleted') !== -1 || lowerMsg.indexOf('removed') !== -1) {
              type = 'deleted';
            }
            // Dynamically get the resource type from session (default to 'resource' if not provided)
            let resource = {!! json_encode(session('resource') ?? 'resource') !!};
            // Show the global success modal using the dynamic resource type
            showGlobalSuccessModal(type, message, resource);
          });
        </script>
      @endif
    </div>
  </main>

  <script>
    $(document).on('click', '.delete-button', function(e) {
        e.preventDefault();
        console.log("Delete button clicked");  // Debug log
        var action = $(this).data('action');
        console.log("Action URL:", action);
        showDeleteConfirmationModal('brand', function(itemType) {
            console.log("Delete confirmed for", itemType);
            $('#globalDeleteForm').attr('action', action).submit();
        });
    });
  </script>

  @yield('scripts')
  
</body>
</html>
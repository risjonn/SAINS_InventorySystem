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
    @vite(['resources/css/app.css'])
</head>
<body class="bg-light" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileSidebarOpen: false }" 
      @resize.window="sidebarOpen = window.innerWidth >= 1024" x-cloak>
    <!-- Sidebar Backdrop (mobile only) -->
    <div x-show="mobileSidebarOpen" 
         @click="mobileSidebarOpen = false" 
         class="lg:hidden fixed inset-0 bg-black/50 z-30 transition-opacity"
         x-transition.opacity></div>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 z-40 h-screen bg-white shadow-lg border-r border-gray-100 overflow-hidden"
           :class="sidebarOpen ? 'w-64' : 'w-20'"
           style="transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="relative">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
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
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors 
                          {{ request()->routeIs('dashboard') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
                    <div class="w-8 flex-shrink-0 flex justify-center">
                        <i class="fas fa-home text-lg"></i>
                        <!-- Alternatively, use an image -->
                        <!--
                        <img 
                            src="{{ asset('images/home-icon.png') }}" 
                            alt="Dashboard Icon" 
                            class="w-6 h-6"
                        >
                        -->
                    </div>
                    <span class="sidebar-text ml-3 text-sm font-medium whitespace-nowrap 
                          transition-all duration-300 origin-left overflow-hidden"
                          :class="sidebarOpen ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0 absolute'">
                        Dashboard
                    </span>
                </a>
                <a href="{{ route('stocks.index') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors 
                          {{ request()->routeIs('stocks.*') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
                    <div class="w-8 flex-shrink-0 flex justify-center">
                        <i class="fas fa-boxes text-lg"></i>
                        <!-- Alternatively, use an image -->
                        <!--
                        <img 
                            src="{{ asset('images/boxes-icon.png') }}" 
                            alt="Stock Inventory Icon" 
                            class="w-6 h-6"
                        >
                        -->
                    </div>
                    <span class="sidebar-text ml-3 text-sm font-medium whitespace-nowrap 
                          transition-all duration-300 origin-left overflow-hidden"
                          :class="sidebarOpen ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0 absolute'">
                        Stock Inventory
                    </span>
                </a>
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors 
                          {{ request()->routeIs('profile.edit') ? 'bg-gray-50 text-blue-600' : 'text-gray-600' }}">
                    <div class="w-8 flex flex-shrink-0 justify-center">
                        <i class="fas fa-user-cog text-lg"></i>
                        <!-- Alternatively, use an image -->
                        <!--
                        <img 
                            src="{{ asset('images/profile-icon.png') }}" 
                            alt="Profile Settings Icon" 
                            class="w-6 h-6"
                        >
                        -->
                    </div>
                    <span class="sidebar-text ml-3 text-sm font-medium whitespace-nowrap 
                          transition-all duration-300 origin-left overflow-hidden"
                          :class="sidebarOpen ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0 absolute'">
                        Profile Settings
                    </span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors text-gray-600">
                        <div class="w-8 flex-shrink-0 flex justify-center">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                            <!-- Alternatively, use an image -->
                            <!--
                            <img 
                                src="{{ asset('images/logout-icon.png') }}" 
                                alt="Logout Icon" 
                                class="w-6 h-6"
                            >
                            -->
                        </div>
                        <span class="sidebar-text ml-3 text-sm font-medium whitespace-nowrap 
                              transition-all duration-300 origin-left overflow-hidden"
                              :class="sidebarOpen ? 'opacity-100 scale-x-100' : 'opacity-0 scale-x-0 absolute'">
                            Logout
                        </span>
                    </button>
                </form>
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

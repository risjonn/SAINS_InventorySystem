@extends('layouts.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen">
        <h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mb-8">Use the side menu to navigate to other sections of the application.</p>

        <!-- Quick Overview Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" id="dashboard-stats">
            <!-- Total Stocks Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Total Stocks</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="total-stocks" data-stat>{{ $stats['totalStocks'] }}</p>
                    <div id="total-stocks-loader" class="loader hidden"></div>
                </div>
            </div>
        
            <!-- Low Stock Items Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Low Stock Items</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="low-stocks" data-stat>{{ $stats['lowStockItems'] }}</p>
                    <div id="low-stocks-loader" class="loader hidden"></div>
                </div>
            </div>
        
            <!-- Users Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Users</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="total-users" data-stat>{{ $stats['totalUsers'] }}</p>
                    <div id="total-users-loader" class="loader hidden"></div>
                </div>
            </div>
        
            <!-- Recent Updates Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Recent Updates</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="recent-updates" data-stat>{{ $stats['recentUpdates'] }}</p>
                    <div id="recent-updates-loader" class="loader hidden"></div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-sm text-gray-500" id="last-updated">
            Last updated: <span id="update-time">{{ now()->format('H:i:s') }}</span>
        </div>
    </div>

    @section('scripts')
        <script>
            function updateDashboardStats() {
                const stats = ['total-stocks', 'low-stocks', 'total-users', 'recent-updates'];
                
                // Show loaders
                stats.forEach(stat => {
                    const loader = document.getElementById(`${stat}-loader`);
                    if (loader) {
                        loader.classList.remove('hidden');
                        loader.classList.add('active');
                    }
                });

                fetch("{{ route('dashboard.stats') }}", {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    cache: 'no-store' // Prevent caching
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    // Force update all numbers
                    document.getElementById('total-stocks').textContent = data.totalStocks;
                    document.getElementById('low-stocks').textContent = data.lowStockItems;
                    document.getElementById('total-users').textContent = data.totalUsers;
                    document.getElementById('recent-updates').textContent = data.recentUpdates;
                    document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
                })
                .catch(console.error)
                .finally(() => {
                    stats.forEach(stat => {
                        const loader = document.getElementById(`${stat}-loader`);
                        if (loader) {
                            loader.classList.remove('active');
                            loader.classList.add('hidden');
                        }
                    }); 
                });
            }

            // Update every 30 seconds
            setInterval(updateDashboardStats, 30000);
        </script>
    @endsection
@endsection
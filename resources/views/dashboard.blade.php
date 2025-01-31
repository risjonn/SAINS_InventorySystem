@extends('layouts.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen">
        <h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mb-8">Use the side menu to navigate to other sections of the application.</p>

        <!-- Quick Overview Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" id="dashboard-stats">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-sm font-semibold text-gray-500">Total Stocks</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900" id="total-stocks">{{ $stats['totalStocks'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-sm font-semibold text-gray-500">Low Stock Items</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900" id="low-stocks">{{ $stats['lowStockItems'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-sm font-semibold text-gray-500">Users</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900" id="total-users">{{ $stats['totalUsers'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-sm font-semibold text-gray-500">Recent Updates</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900" id="recent-updates">{{ $stats['recentUpdates'] }}</p>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        function updateDashboardStats() {
            fetch("{{ route('dashboard.stats') }}", {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                document.getElementById('total-stocks').textContent = data.totalStocks;
                document.getElementById('low-stocks').textContent = data.lowStockItems;
                document.getElementById('total-users').textContent = data.totalUsers;
                document.getElementById('recent-updates').textContent = data.recentUpdates;
            })
            .catch(error => console.error('Error fetching stats:', error));
        }

        // Update every 30 seconds (adjust as needed)
        setInterval(updateDashboardStats, 30000);
        
        // Initial load when page is ready
        document.addEventListener('DOMContentLoaded', updateDashboardStats);
    </script>
    @endsection
@endsection
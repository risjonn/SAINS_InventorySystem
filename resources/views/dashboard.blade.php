@extends('layouts.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen">
        <h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mb-8">Use the dropdown menu below to select a section.</p>

        <!-- Section Selection Dropdown -->
        <div class="mb-6">
            <label for="sectionSelect" class="block text-sm font-semibold text-gray-700">Select Section</label>
            <select id="sectionSelect" class="mt-1 block w-full p-2 border rounded-md">
                <option value="" selected disabled>Select a section</option>
                <optgroup label="Sarawak">
                    <option value="Kuching">Kuching</option>
                    <option value="Sri Aman">Sri Aman</option>
                    <option value="Betong">Betong</option>
                    <option value="Sarikei">Sarikei</option>
                    <option value="Sibu">Sibu</option>
                    <option value="Mukah">Mukah</option>
                    <option value="Kapit">Kapit</option>
                    <option value="Bintulu">Bintulu</option>
                    <option value="Miri">Miri</option>
                    <option value="Limbang">Limbang</option>
                </optgroup>
                <optgroup label="Sabah">
                    <option value="Sandakan">Sandakan</option>
                    <option value="Tawau">Tawau</option>
                </optgroup>
            </select>
        </div>

        <!-- Quick Overview Cards (Hidden by Default) -->
        <div id="dashboard-stats" class="hidden grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Stocks Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Total Stocks</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="total-stocks">0</p>
                </div>
            </div>

            <!-- Low Stock Items Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Low Stock Items</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="low-stocks">0</p>
                </div>
            </div>

            <!-- Users Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Users</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="total-users">0</p>
                </div>
            </div>

            <!-- Recent Updates Card -->
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Recent Updates</h3>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-3xl font-bold text-blue-600" id="recent-updates">0</p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-sm text-gray-500 hidden" id="last-updated">
            Last updated: <span id="update-time">{{ now()->format('H:i:s') }}</span>
        </div>
    </div>

    @section('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const sectionSelect = document.getElementById("sectionSelect");
                const statsContainer = document.getElementById("dashboard-stats");
                const lastUpdated = document.getElementById("last-updated");

                // Function to update stats
                function updateDashboardStats(section) {
                    if (!section) return;

                    // Show dashboard stats
                    statsContainer.classList.remove("hidden");
                    lastUpdated.classList.remove("hidden");

                    fetch("{{ route('dashboard.stats') }}?section=" + encodeURIComponent(section), {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        cache: 'no-store'
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('total-stocks').textContent = data.totalStocks;
                        document.getElementById('low-stocks').textContent = data.lowStockItems;
                        document.getElementById('total-users').textContent = data.totalUsers;
                        document.getElementById('recent-updates').textContent = data.recentUpdates;
                        document.getElementById('update-time').textContent = new Date().toLocaleTimeString();
                    })
                    .catch(console.error);
                }

                // Check if a section is saved in localStorage
                const savedSection = localStorage.getItem("selectedSection");
                if (savedSection) {
                    sectionSelect.value = savedSection;
                    updateDashboardStats(savedSection);
                }

                // Update when a new section is selected
                sectionSelect.addEventListener("change", function () {
                    const selectedSection = sectionSelect.value;
                    localStorage.setItem("selectedSection", selectedSection);
                    updateDashboardStats(selectedSection);
                });

                // Auto-update every 30 seconds
                setInterval(() => {
                    const selectedSection = localStorage.getItem("selectedSection");
                    if (selectedSection) updateDashboardStats(selectedSection);
                }, 30000);

                // Clear section selection on logout
                const logoutButton = document.getElementById("logoutButton");
                if (logoutButton) {
                    logoutButton.addEventListener("click", function () {
                        localStorage.removeItem("selectedSection");
                    });
                }
            });
        </script>
    @endsection
@endsection

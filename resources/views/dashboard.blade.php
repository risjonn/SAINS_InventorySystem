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
            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Total Stocks</h3>
                <p class="text-3xl font-bold text-blue-600" id="total-stocks">0</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md relative border-l-4 border-yellow-500 low-stock-card">
                <h3 class="text-sm font-semibold text-yellow-600">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Items
                </h3>
                <p class="text-3xl font-bold text-yellow-600" id="low-stocks">0</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Users</h3>
                <p class="text-3xl font-bold text-blue-600" id="total-users">0</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md relative">
                <h3 class="text-sm font-semibold text-gray-500">Recent Updates</h3>
                <p class="text-3xl font-bold text-blue-600" id="recent-updates">0</p>
            </div>
        </div>

        <!-- Stock List (Hidden if no data) -->
        <div id="stock-list" class="hidden mt-8">
            <h3 class="text-lg font-semibold mb-4">Stock List</h3>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2">Item Name</th>
                        <th class="border border-gray-300 px-4 py-2">Quantity</th>
                        <th class="border border-gray-300 px-4 py-2">Last Updated</th>
                    </tr>
                </thead>
                <tbody id="stock-data"></tbody>
            </table>
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
        const stockList = document.getElementById("stock-list");
        const lastUpdated = document.getElementById("last-updated");

        function updateDashboardStats(section) {
            if (!section) return;

            statsContainer.classList.remove("hidden");
            lastUpdated.classList.remove("hidden");

            fetch("{{ route('dashboard.stats') }}?section=" + encodeURIComponent(section))
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-stocks').textContent = data.totalStocks;
                    document.getElementById('low-stocks').textContent = data.lowStockItems;
                    document.getElementById('total-users').textContent = data.totalUsers;
                    document.getElementById('recent-updates').textContent = data.recentUpdates;
                });

            fetch("{{ route('dashboard.stocks') }}?section=" + encodeURIComponent(section))
                .then(response => response.json())
                .then(data => {
                    let stockTable = document.getElementById("stock-data");
                    stockTable.innerHTML = "";

                    if (data.length > 0) {
                        stockList.classList.remove("hidden");
                        data.forEach(stock => {
                            stockTable.innerHTML += `<tr>
                                <td class="border px-4 py-2">${stock.name}</td>
                                <td class="border px-4 py-2">${stock.quantity}</td>
                                <td class="border px-4 py-2">${stock.updated_at}</td>
                            </tr>`;
                        });
                    } else {
                        stockList.classList.add("hidden");
                    }
                });
        }

        // ✅ Ensure section persists across pages
        const savedSection = sessionStorage.getItem("selectedSection");
        if (savedSection) {
            sectionSelect.value = savedSection;
            updateDashboardStats(savedSection);
        }

        sectionSelect.addEventListener("change", function () {
            const selectedSection = this.value;
            sessionStorage.setItem("selectedSection", selectedSection);
            updateDashboardStats(selectedSection);
        });

        // ✅ Clear section on logout by listening for logout button
        document.querySelector("form[action='{{ route('logout') }}']").addEventListener("submit", function () {
            sessionStorage.removeItem("selectedSection"); // ✅ Remove section on logout
        });
    });
</script>

    @endsection
@endsection

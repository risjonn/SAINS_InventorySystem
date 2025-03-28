@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 my-5">
    <!-- Back to Dashboard Button -->
    <div class="my-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>
    <h2 class="text-2xl font-semibold mt-4">Low Stock Summary by Division</h2>

    <!-- Table Section -->
    <div class="mt-4 bg-white rounded-xl shadow-lg p-6">
        <table class="w-full mt-4 border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3 border-b">Division</th>
                    <th class="p-3 border-b">Low Stock Items</th>
                    <th class="p-3 border-b">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($divisionsSummary as $division)
                <tr class="border-b odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-all duration-150">
                    <td class="p-3 border-b">{{ $division['name'] }}</td>
                    <td class="p-3 border-b text-left">
                        <div class="flex items-center space-x-2">
                            <!-- Non-critical Low Badge -->
                            <div class="flex items-center bg-amber-100 text-amber-800 px-3 py-1 rounded-full border border-amber-600 shadow">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span class="font-semibold">Low: {{ $division['nonCriticalLow'] }}</span>
                            </div>
                            <!-- Critical Badge -->
                            <div class="flex items-center bg-red-100 text-red-800 px-3 py-1 rounded-full border border-red-600 shadow">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <span class="font-semibold">Critical: {{ $division['critical'] }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-3 border-b text-left">
                        <a href="{{ route('stocks.low', ['division' => $division['id']]) }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition-colors duration-200 text-sm">
                            <i class="fas fa-eye mr-1"></i>
                            <span>View Details</span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Chart Section: Placed below the table and centered with a max-width -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6" style="max-width: 600px; margin: auto; height: 300px;">
        <canvas id="lowStockChart" style="max-width: 100%; height: 100%;"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pass divisionsSummary from PHP to JavaScript
    const divisionsSummary = @json($divisionsSummary);
    const labels = divisionsSummary.map(item => item.name);
    const nonCriticalData = divisionsSummary.map(item => item.nonCriticalLow);
    const criticalData = divisionsSummary.map(item => item.critical);

    const ctx = document.getElementById('lowStockChart').getContext('2d');
    const lowStockChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Low Stock',
                    data: nonCriticalData,
                    backgroundColor: 'rgba(251, 191, 36, 0.6)', // amber
                    borderColor: 'rgba(251, 191, 36, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Critical Stock',
                    data: criticalData,
                    backgroundColor: 'rgba(239, 68, 68, 0.6)', // red
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allows us to control size via container CSS
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    }
                }
            }
        }
    });
</script>
@endsection

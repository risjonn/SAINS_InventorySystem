@extends('layouts.layout')

@section('content')
<style>
    .animate-progress {
        transition: width 0.5s ease-out;
    }
    
    /* Synchronized glow animation */
    @keyframes sync-glow-pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        50% { box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.4); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    
    @keyframes pulse-fade {
        0% { opacity: 0.8; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.15); }
        100% { opacity: 0.8; transform: scale(1); }
    }
    
    @keyframes subtle-shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-1px) rotate(-1deg); }
        50% { transform: translateX(0); }
        75% { transform: translateX(1px) rotate(1deg); }
        100% { transform: translateX(0); }
    }
    
    @keyframes glow-blink {
        0%, 100% { opacity: 1; text-shadow: 0 0 0 rgba(220, 38, 38, 0); }
        50% { opacity: 1; text-shadow: 0 0 10px rgba(220, 38, 38, 0.8); }
    }
    
    .critical-glow {
        position: relative;
        animation: sync-glow-pulse 1.5s infinite ease-in-out;
        border-radius: 9999px;
    }
    
    /* Collapsible panel for thresholds */
    .collapse-content {
        max-height: 300px;
        overflow-y: auto;
    }
</style>

<script>
    // Ensure window.categories is defined
    window.categories = {!! json_encode($distinctCategories) !!};
</script>

<div class="container mx-auto px-4 my-5">
    <!-- Back Button -->
    @if(auth()->user()->role === 'Super Admin')
        <a href="{{ route('stocks.low', ['division' => 'all']) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Back to Summary
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    @endif

    @if(auth()->user()->role === 'Super Admin' && isset($divisions))
        <div class="mb-4">
            <form method="GET" action="{{ route('stocks.low') }}">
                <label for="division" class="block text-sm font-medium text-gray-700">Select Division:</label>
                <select name="division" id="division" onchange="this.form.submit()" class="block w-full mt-1 border rounded p-2">
                    <option value="all" {{ $selectedDivision === 'all' ? 'selected' : '' }}>All Divisions</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ $selectedDivision == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif

    <h2 class="text-2xl font-semibold mt-4">Low Stock Items</h2>

    <!-- Collapsible Threshold Settings Section -->
    <div x-data="thresholdSettings()" class="mb-6">
        <button @click="showSettings = !showSettings" class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded transition-colors duration-200">
            <i class="fas fa-sliders-h mr-2"></i>
            <span x-text="showSettings ? 'Hide Threshold Settings' : 'Edit Threshold Settings'"></span>
        </button>
        <div x-show="showSettings" class="mt-4 bg-white rounded-xl shadow-lg p-4 collapse-content" style="display: none;">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2 text-gray-800">Threshold Settings (per Category)</h3>
            <template x-for="(setting, index) in thresholds" :key="setting.category">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-1/3">
                        <span x-text="setting.category" class="font-medium text-gray-800"></span>
                    </div>
                    <div class="w-1/3">
                        <label class="block text-sm text-gray-600">Low Threshold:</label>
                        <select x-model="setting.low_threshold" @change="updateThreshold(setting)" class="mt-1 block w-full border rounded p-2">
                            <template x-for="i in 11" :key="i">
                                <option :value="i-1" x-text="i-1"></option>
                            </template>
                        </select>
                    </div>
                    <div class="w-1/3">
                        <label class="block text-sm text-gray-600">Critical Level:</label>
                        <select x-model="setting.critical_threshold" @change="updateThreshold(setting)" class="mt-1 block w-full border rounded p-2">
                            <template x-for="i in 4" :key="i">
                                <option :value="i-1" x-text="i-1"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Table: Only show categories below threshold (default low threshold: 10) -->
    <div class="mt-4 bg-white rounded-xl shadow-lg p-6">
        @if(isset($division))
            <p class="text-lg text-gray-700 mb-2">Division: {{ $division->name }}</p>
        @else
            <p class="text-lg text-gray-700 mb-2">All Divisions</p>
        @endif

        <table class="w-full mt-4 border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3 border-b">Category</th>
                    <th class="p-3 border-b">Items Available</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aggregatedStocks as $stockGroup)
                    @if($stockGroup->total < 10)
                    <tr class="border-b odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-all duration-150 relative group">
                        <td class="p-3 border-b">{{ $stockGroup->category }}</td>
                        <td class="p-3 border-b text-left">
                            <div class="flex items-center space-x-2">
                                <!-- Display available count -->
                                <span class="w-8 inline-block font-medium text-amber-600 text-center">
                                    {{ $stockGroup->total }}
                                </span>
                                
                                <!-- Progress Bar (fills proportionally toward 10) -->
                                <div class="w-24 h-3 bg-gray-200 rounded-full overflow-hidden {{ $stockGroup->total <= 5 ? 'critical-glow' : '' }}">
                                    <div class="h-full rounded-full {{ $stockGroup->total <= 5 ? 'bg-red-600' : 'bg-amber-500' }} animate-progress"
                                        style="width: {{ min(($stockGroup->total / 10) * 100, 100) }}%;">
                                    </div>
                                </div>
                                
                                <!-- Status Badge -->
                                <div>
                                    @if($stockGroup->total <= 5)
                                        <span class="inline-flex items-center bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold shadow">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Critical
                                        </span>
                                    @elseif($stockGroup->total < 10)
                                        <span class="inline-flex items-center bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs font-semibold shadow">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Low
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>                   
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Enhanced Chart Section: Similar styling as summary page -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6" style="max-width: 600px; margin: auto; height: 300px;">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 text-center">Low Stock by Category</h3>
        <canvas id="lowStockChart" style="max-width: 100%; height: 100%;"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function thresholdSettings() {
        return {
            showSettings: false,
            thresholds: [],
            init() {
                console.log('Alpine init triggered');
                fetch('{{ route('thresholds.user') }}', {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched thresholds:', data);
                    let fetched = Object.values(data);
                    if (window.categories && window.categories.length > 0) {
                        this.thresholds = window.categories.map(cat => {
                            let found = fetched.find(item => item.category === cat);
                            return found ? found : {
                                category: cat,
                                low_threshold: 10,
                                critical_threshold: 3
                            };
                        });
                    } else {
                        this.thresholds = fetched;
                    }
                })
                .catch(error => console.error('Error fetching thresholds:', error));
            },
            updateThreshold(setting) {
                fetch('{{ route('thresholds.update') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        category: setting.category,
                        low_threshold: setting.low_threshold,
                        critical_threshold: setting.critical_threshold
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Threshold updated for ' + setting.category);
                    }
                })
                .catch(error => console.error('Error updating threshold:', error));
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('lowStockChart').getContext('2d');
    const lowStockChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($categoriesLabels) !!},
            datasets: [{
                label: 'Stock Count',
                data: {!! json_encode($categoriesData) !!},
                backgroundColor: [
                    'rgba(248, 113, 113, 0.7)',
                    'rgba(251, 191, 36, 0.7)',
                    'rgba(52, 211, 153, 0.7)',
                    'rgba(96, 165, 250, 0.7)',
                    'rgba(167, 139, 250, 0.7)',
                    // Add more colors if needed...
                ],
                borderColor: [
                    'rgba(248, 113, 113, 1)',
                    'rgba(251, 191, 36, 1)',
                    'rgba(52, 211, 153, 1)',
                    'rgba(96, 165, 250, 1)',
                    'rgba(167, 139, 250, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 20,
                    right: 20,
                    bottom: 20,
                    left: 20
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Low Stock by Category'
                }
            }
        }
    });
</script>
@endsection

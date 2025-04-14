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

    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    @if(auth()->user()->role === 'Super Admin' && isset($selectedAdmin))
         window.editingUser = {{ $selectedAdmin->id }};
    @else
         window.editingUser = {{ auth()->user()->id }};
    @endif
</script>

<script>
    // Ensure window.categories is defined
    window.categories = {!! json_encode($distinctCategories) !!};
</script>

<div class="container mx-auto px-4 my-5">
    <!-- Back Button -->
    @if(auth()->user()->role === 'Super Admin')
        <a href="{{ route('stocks.low', ['division' => 'all']) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Summary
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

    {{-- @if(auth()->user()->role === 'Super Admin' && isset($admins))
        <div class="mb-4">
            <label for="adminSelector" class="block text-sm font-medium text-gray-700">Select Admin:</label>
            <select id="adminSelector" onchange="changeAdminThresholds(this.value)" class="block w-full mt-1 border rounded p-2">
                @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ (isset($selectedAdmin) && $selectedAdmin->id == $admin->id) ? 'selected' : '' }}>{{ $admin->name }}</option>
                @endforeach
            </select>
        </div>
    @endif --}}

    <!-- Collapsible Threshold Settings Section -->
    <div x-data="thresholdSettings()" x-init="init()" class="mb-6 max-w-md mx-auto relative">
        <!-- Card Title -->
        <h2 class="text-xl font-bold text-gray-800 mb-2">Set Custom Thresholds per Category</h2>
        
        <!-- Toggle Button with Icon -->
        <button @click="showSettings = !showSettings" class="w-full py-2 bg-indigo-600 text-white font-medium rounded flex justify-between items-center px-3">
            <span x-text="showSettings ? 'Hide Settings' : 'Edit Settings'"></span>
            <i :class="showSettings ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
        
        <!-- Add extra spacing below the toggle header -->
        <div x-show="showSettings" x-cloak class="mt-4 p-3 bg-white border rounded shadow" style="max-height: 300px; overflow-y: auto;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="(setting, index) in thresholds" :key="setting.category">
                    <div class="border p-2 rounded">
                        <!-- Category Name -->
                        <div class="mb-1">
                            <span class="font-semibold text-gray-700" x-text="setting.category"></span>
                        </div>
                        <!-- Low Threshold Control -->
                        <div class="flex items-center mb-1">
                            <label class="text-xs text-gray-600 w-20" title="Low threshold value">Low:</label>
                            <select x-model="setting.low_threshold" class="w-14 border rounded text-sm p-1 focus:outline-none">
                                <template x-for="i in 11" :key="i">
                                    <option :value="i-1" x-text="i-1"></option>
                                </template>
                            </select>
                        </div>
                        <!-- Critical Threshold Control -->
                        <div class="flex items-center">
                            <label class="text-xs text-gray-600 w-20" title="Critical threshold value">Critical:</label>
                            <select x-model="setting.critical_threshold" class="w-14 border rounded text-sm p-1 focus:outline-none">
                                <template x-for="i in 4" :key="i">
                                    <option :value="i-1" x-text="i-1"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </template>
            </div>
            <!-- Save Button -->
            <div class="mt-4 flex justify-end items-center space-x-2">
                <div x-show="toastVisible" x-transition.duration.500ms class="p-2 bg-green-500 text-white text-xs rounded shadow">
                    ✅ Thresholds updated successfully.
                </div>
                <button @click="saveAll()" class="py-1 px-3 bg-green-600 hover:bg-green-700 text-white text-sm rounded shadow flex items-center justify-center">
                    <i class="fas fa-save mr-1"></i>
                    <span x-text="saveButtonText"></span>
                </button>
            </div>
        </div>

        <!-- Full-page overlay to indicate reload -->

        <div x-show="isReloading" x-cloak :class="{'hidden': !isReloading}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="text-white text-xl font-semibold">Reloading...</div>
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
                @php
                    // Normalize the category key to retrieve the threshold settings.
                    $catKey = strtolower(trim($stockGroup->category));
                    // Set defaults if not defined.
                    $lowThreshold = isset($thresholdsForUser[$catKey]) ? $thresholdsForUser[$catKey]->low_threshold : 10;
                    $criticalThreshold = isset($thresholdsForUser[$catKey]) ? $thresholdsForUser[$catKey]->critical_threshold : 3;
                @endphp
            
                    {{-- Only display the row if stock total is less than the current low threshold --}}
                    @if($stockGroup->total < $lowThreshold)
                    <tr class="border-b odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-all duration-150 relative group">
                        <td class="p-3 border-b">{{ $stockGroup->category }}</td>
                        <td class="p-3 border-b text-left">
                            <div class="flex items-center space-x-2">
                                <!-- Display available count -->
                                <span class="w-8 inline-block font-medium text-amber-600 text-center">
                                    {{ $stockGroup->total }}
                                </span>
                                
                                <!-- Progress Bar: fill width computed as a percentage of the lowThreshold -->
                                <div class="w-24 h-3 bg-gray-200 rounded-full overflow-hidden {{ $stockGroup->total <= $criticalThreshold ? 'critical-glow' : '' }}">
                                    <div class="h-full rounded-full {{ $stockGroup->total <= $criticalThreshold ? 'bg-red-600' : 'bg-amber-500' }} animate-progress"
                                        style="width: {{ min(($stockGroup->total / $lowThreshold) * 100, 100) }}%;">
                                    </div>
                                </div>
                                
                                <!-- Status Badge -->
                                <div>
                                    @if($stockGroup->total <= $criticalThreshold)
                                        <span class="inline-flex items-center bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold shadow">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Critical
                                        </span>
                                    @elseif($stockGroup->total < $lowThreshold)
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
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6" style="max-width: 600px; margin: auto; height: 400px;">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 text-center">Low Stock by Category</h3>
        <canvas id="lowStockChart" width="600" height="400"></canvas>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        function thresholdSettings() {
            return {
                showSettings: false,
                thresholds: [],
                saveButtonText: "Save Settings",
                toastVisible: false,
                isReloading: false,
                init() {
                    console.log('Alpine init triggered');
                    // Build URL with target user if necessary.
                    let url = '{{ route('thresholds.user') }}';
                    if (window.editingUser && window.editingUser != {{ auth()->user()->id }}) {
                        url += '?user_id=' + window.editingUser;
                    }
                    fetch(url, { headers: {'X-Requested-With': 'XMLHttpRequest'} })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Fetched thresholds:', data);
                            let fetched = Object.values(data);
                            if (window.categories && window.categories.length > 0) {
                                this.thresholds = window.categories.map(cat => {
                                    let found = fetched.find(item => {
                                        return item.category.trim().toLowerCase() === cat.trim().toLowerCase();
                                    });
                                    if (found) {
                                        if (parseInt(found.low_threshold, 10) === 0) {
                                            found.low_threshold = 10;
                                        }
                                        if (parseInt(found.critical_threshold, 10) === 0) {
                                            found.critical_threshold = 3;
                                        }
                                        return found;
                                    }
                                    return {
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
                    let payload = {
                        category: setting.category,
                        low_threshold: setting.low_threshold,
                        critical_threshold: setting.critical_threshold
                    };
                    if (window.editingUser && window.editingUser != {{ auth()->user()->id }}) {
                        payload.user_id = window.editingUser;
                    }
                    fetch('{{ route('thresholds.update') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Threshold updated for ' + setting.category);
                            }
                        })
                        .catch(error => console.error('Error updating threshold:', error));
                },
                saveAll() {
                    // Create an array of promises for all fetch requests.
                    let promises = this.thresholds.map(setting => {
                        let payload = {
                            category: setting.category,
                            low_threshold: setting.low_threshold,
                            critical_threshold: setting.critical_threshold
                        };
                        if (window.editingUser && window.editingUser != {{ auth()->user()->id }}) {
                            payload.user_id = window.editingUser;
                        }
                        return fetch('{{ route('thresholds.update') }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        }).then(response => response.json());
                    });
                    // Set button to "Saving..."
                    this.saveButtonText = "Saving...";
                    Promise.all(promises).then(() => {
                        this.saveButtonText = "Saved!";
                        this.toastVisible = true;
                        // Keep the toast visible for 1.5 seconds
                        setTimeout(() => {
                            // Fade out the toast over 300ms
                            this.toastVisible = false;
                            setTimeout(() => {
                                // Reset the button text and show the reloading overlay
                                this.saveButtonText = "Save Settings";
                                this.isReloading = true;
                                // Wait 800ms for the reloading overlay to be seen before reloading
                                setTimeout(() => {
                                    window.location.reload();
                                }, 800);
                            }, 300);
                        }, 1500);
                    }).catch(error => {
                        console.error('Error saving thresholds:', error);
                        this.saveButtonText = "Save Settings";
                    });
                }
            };
        }
    </script> 

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Log the data arrays to ensure they contain values.
        const labels = {!! json_encode($categoriesLabels) !!};
        const dataValues = {!! json_encode($categoriesData) !!};
        console.log("Chart Labels:", labels);
        console.log("Chart Data:", dataValues);

        const canvas = document.getElementById('lowStockChart');
        if (!canvas) {
            console.error("Canvas element with id 'lowStockChart' not found.");
            return;
        }
        const ctx = canvas.getContext('2d');
        
        const lowStockChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Stock Count',
                    data: dataValues,
                    backgroundColor: [
                        'rgba(248, 113, 113, 0.7)',
                        'rgba(251, 191, 36, 0.7)',
                        'rgba(52, 211, 153, 0.7)',
                        'rgba(96, 165, 250, 0.7)',
                        'rgba(167, 139, 250, 0.7)',
                        // More colors if needed...
                    ],
                    borderColor: [
                        'rgba(248, 113, 113, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(52, 211, 153, 1)',
                        'rgba(96, 165, 250, 1)',
                        'rgba(167, 139, 250, 1)',
                    ],
                    borderWidth: 1,
                    barPercentage: 0.6,
                    categoryPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 30,
                        right: 30,
                        bottom: 30,
                        left: 30
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            padding: 10
                        },
                        grid: {
                            display: true,
                            color: "rgba(0, 0, 0, 0.1)"
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            padding: 10
                        },
                        grid: {
                            display: true,
                            color: "rgba(0, 0, 0, 0.1)"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Low Stock by Category',
                        padding: { bottom: 20 }
                    }
                }
            }
        });
    });
    </script>
@endsection
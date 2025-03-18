@extends('layouts.layout')

@section('content')
<style>
    .animate-progress {
        transition: width 0.5s ease-out;
    }
    
    /* Synchronized glow animation */
    @keyframes sync-glow-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
        }
        50% {
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.4);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
        }
    }
    
    /* Pulse animation that changes opacity and scale */
    @keyframes pulse-fade {
        0% {
            opacity: 0.8;
            transform: scale(1);
        }
        50% {
            opacity: 1;
            transform: scale(1.15);
        }
        100% {
            opacity: 0.8;
            transform: scale(1);
        }
    }
    
    /* Subtle shake animation */
    @keyframes subtle-shake {
        0% {
            transform: translateX(0);
        }
        25% {
            transform: translateX(-1px) rotate(-1deg);
        }
        50% {
            transform: translateX(0);
        }
        75% {
            transform: translateX(1px) rotate(1deg);
        }
        100% {
            transform: translateX(0);
        }
    }
    
    /* Blink animation with shadow */
    @keyframes glow-blink {
        0%, 100% {
            opacity: 1;
            text-shadow: 0 0 0 rgba(220, 38, 38, 0);
        }
        50% {
            opacity: 1;
            text-shadow: 0 0 10px rgba(220, 38, 38, 0.8);
        }
    }
    
    .critical-glow {
        position: relative;
        animation: sync-glow-pulse 1.5s infinite ease-in-out;
        border-radius: 9999px;
    }
    
    .pulse-fade {
        display: inline-block;
        animation: pulse-fade 1.5s infinite ease-in-out;
        transform-origin: center;
    }
    
    .subtle-shake {
        display: inline-block;
        animation: subtle-shake 1.5s infinite ease-in-out;
    }
    
    .glow-blink {
        display: inline-block;
        animation: glow-blink 1.5s infinite ease-in-out;
    }
</style>

<div class="container mx-auto px-4 my-5">
    <!-- Back to Summary/Dashboard Button -->
    @if(auth()->user()->role === 'Super Admin')
        <a href="{{ route('stocks.low', ['division' => 'all']) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Summary
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
    @endif

    <h2 class="text-2xl font-semibold mt-4">Low Stock Items</h2>

    <!-- For Super Admin, add a division selection dropdown -->
    @if(auth()->user()->role === 'Super Admin' && isset($divisions))
        <div class="mb-4">
            <form method="GET" action="{{ route('stocks.low') }}">
                <label for="division" class="block text-sm font-medium text-gray-700">Select Division:</label>
                <select name="division" id="division" onchange="this.form.submit()" class="block w-full mt-1 border rounded p-2">
                    <option value="all" {{ $selectedDivision === 'all' ? 'selected' : '' }}>All Divisions</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ $selectedDivision == $div->id ? 'selected' : '' }}>
                            {{ $div->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif

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
                @foreach($stocks as $stock)
                <tr class="border-b odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-all duration-150 relative group">
                    <td class="p-3 border-b">{{ $stock->category }}</td>
                    <td class="p-3 border-b text-left">
                        <div class="flex items-center space-x-2">
                            <!-- Fixed-width container for number -->
                            <span class="text-left w-6 inline-block font-medium
                                {{ $stock->total <= 5 ? 'text-red-600' : ($stock->total < 10 ? 'text-amber-600' : 'text-green-600') }}">
                                {{ $stock->total }}
                            </span>
                            
                            <!-- Progress bar container -->
                            <div class="w-24 h-3 bg-gray-200 rounded-full overflow-hidden {{ $stock->total <= 5 ? 'critical-glow' : '' }}">
                                <!-- Inner bar -->
                                <div class="h-full rounded-full animate-progress 
                                     {{ $stock->total <= 5 ? 'bg-red-600' : ($stock->total < 10 ? 'bg-amber-500' : 'bg-green-500') }}"
                                     data-target-width="{{ min(($stock->total / 10) * 100, 100) }}%"
                                     style="width: 0%;">
                                </div>
                            </div>
                            
                            <!-- Status indicator -->
                            @if($stock->total <= 5)
                                <i class="fas fa-exclamation-triangle text-red-600 pulse-fade" title="Critical Stock Level"></i>
                            @elseif($stock->total < 10)
                                <i class="fas fa-exclamation-circle text-amber-500" title="Low Stock"></i>
                            @else
                                <i class="fas fa-check-circle text-green-500" title="Adequate Stock"></i>
                            @endif
                            
                            <!-- Container for status text and tooltip -->
                            <div class="relative inline-flex items-center">
                                <!-- Text label for stock status -->
                                <span class="text-xs font-medium
                                    {{ $stock->total <= 5 ? 'text-red-600 glow-blink' : ($stock->total < 10 ? 'text-amber-500' : 'text-green-500') }}">
                                    {{ $stock->total <= 5 ? 'Critical' : ($stock->total < 10 ? 'Low' : 'OK') }}
                                </span>
                                
                                <!-- Tooltip -->
                                <div class="opacity-0 group-hover:opacity-100 absolute left-full ml-2 top-1/2 transform -translate-y-1/2 bg-white shadow-lg p-3 rounded-md text-xs z-20 transition-opacity duration-200 min-w-[200px] text-gray-800">
                                    <p class="mb-1"><strong>Category:</strong> {{ $stock->category }}</p>
                                    <p class="mb-1"><strong>Current Stock:</strong> {{ $stock->total }}</p>
                                    <p class="mb-1"><strong>Status:</strong> {{ $stock->total <= 5 ? 'Critical' : ($stock->total < 10 ? 'Low' : 'Adequate') }}</p>
                                    @if(isset($stock->updated_at))
                                        <p class="mb-1"><strong>Last Updated:</strong> {{ $stock->updated_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </td>                   
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-6">
        <canvas id="lowStockChart" class="w-full h-64"></canvas>
    </div>
</div>
@endsection

@section('scripts')
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
                    // Additional colors as needed
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
            maintainAspectRatio: false, 
            indexAxis: 'y',
            responsive: true,
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
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Low Stock by Category'
                }
            }
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.animate-progress').forEach(function(el) {
            const targetWidth = el.getAttribute('data-target-width');
            // Trigger reflow for a smooth transition
            void el.offsetWidth;
            el.style.width = targetWidth;
        });
    });
</script>
@endsection

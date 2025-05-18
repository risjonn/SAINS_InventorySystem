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

    #category-summary-table {
        scroll-margin-top: 4rem; /* adjust to match your header height */
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
            <i class="fas fa-arrow-left mr-2"></i> Summary By Division
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Dashboard
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

    <h1 class="text-2xl font-bold mt-4 mb-4">SKU Status By Category</h1>

    <form method="GET" class="flex flex-wrap justify-center items-center gap-4 mb-6">
        <!-- preserve the division so we stay on this page -->
        <input type="hidden" name="division" value="{{ $selectedDivision }}">

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Search category…"
            class="border-gray-300 focus:border-blue-500 focus:ring-blue-200 rounded-md shadow-sm px-3 py-2"
        >
        <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md px-4 py-2"
        >Apply</button>
    </form>

    <!-- Table -->
    <a id="category-summary-table"></a>
    <div id="category-summary-table" class="mt-4 bg-white rounded-xl shadow-lg p-6">
       <h2 class="text-xl font-semibold text-gray-600 mb-4">Division: {{ $division->name }}</h2>
        <table class="table-auto w-full mb-6">
            <thead>
                <tr class="bg-gray-300">
                    @php
                    // Determine the opposite direction for toggling
                    $dirToggle = request('dir', 'asc') === 'asc' ? 'desc' : 'asc';

                    // Base URL for sorting links
                    $baseUrl = url()->current();
                    @endphp

                    {{-- Category header --}}
                    @php
                    $commonCat = array_merge(request()->all(), [
                        'sort' => 'category',
                        'dir'  => $dirToggle,
                    ]);
                    $queryCat = http_build_query($commonCat);
                    @endphp

                    <th class="px-4 py-2 text-left">
                        <a href="{{ $baseUrl }}?{{ $queryCat }}#category-summary-table" class="inline-flex items-center">
                            <span>Category</span>
                            <i class="fas fa-sort{{ request('sort')==='category'
                                ? (request('dir')==='asc' ? '-up' : '-down')
                                : '' }} ml-1"></i>
                        </a>
                    </th>

                    {{-- Low SKUs header --}}
                    @php
                    $commonLow = array_merge(request()->all(), [
                        'sort' => 'low_count',
                        'dir'  => $dirToggle,
                    ]);
                    $queryLow = http_build_query($commonLow);
                    @endphp

                    <th class="px-4 py-2 text-center">
                        <a href="{{ $baseUrl }}?{{ $queryLow }}#category-summary-table" class="inline-flex items-center">
                            <span>Low SKUs</span>
                            <i class="fas fa-sort{{ request('sort')==='low_count'
                                ? (request('dir')==='asc' ? '-up' : '-down')
                                : '' }} ml-1"></i>
                        </a>
                    </th>

                    {{-- Critical SKUs header --}}
                    @php
                    $commonCrit = array_merge(request()->all(), [
                        'sort' => 'critical_count',
                        'dir'  => $dirToggle,
                    ]);
                    $queryCrit = http_build_query($commonCrit);
                    @endphp

                    <th class="px-4 py-2 text-center">
                        <a href="{{ $baseUrl }}?{{ $queryCrit }}#category-summary-table" class="inline-flex items-center">
                            <span>Critical SKUs</span>
                            <i class="fas fa-sort{{ request('sort')==='critical_count'
                                ? (request('dir')==='asc' ? '-up' : '-down')
                                : '' }} ml-1"></i>
                        </a>
                    </th>

                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr class="bg-white even:bg-gray-100 hover:bg-gray-200">
                    <td class="px-4 py-2">{{ $cat['category'] }}</td>
                    <td class="px-4 py-2 text-center">{{ $cat['low_count'] }}</td>
                    <td class="px-4 py-2 text-center">{{ $cat['critical_count'] }}</td>
                    <td class="px-4 py-2">
                    <a href="{{ route('stocks.low.byCategory', [
                            'division' => $selectedDivision,
                            'category' => $cat['category']
                        ]) }}"
                        class="inline-block px-3 py-1 bg-blue-600 text-white rounded text-sm">
                        View Products
                    </a>
                    </td>
                </tr>
                @endforeach

                @if($categories->isEmpty())
                    <tr><td colspan="4" class="text-center py-4">No categories match your criteria.</td></tr>
                @endif

            </tbody>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $categories->appends(request()->all())->links() }}
            </div>

        </table>
    </div>

    @if(request()->has('sort'))
        <script>
            // Give the browser a moment to render the anchor
            setTimeout(function() {
            window.location.hash = 'category-summary-table';
            }, 50);
        </script>
    @endif

    <!-- Category Flagged‐SKU Chart -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6" style="max-width: auto; margin: auto; height: 400px;">
        <canvas id="categoryStackChart" style="width:100%; height:100%;"></canvas>
    </div>

</div>
@endsection

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Bring your PHP data into JS
    const catData = @json($categoriesSummary);
    const labels   = catData.map(c => c.category);
    const lowData  = catData.map(c => c.low_count);
    const critData = catData.map(c => c.critical_count);

    const ctx = document.getElementById('categoryStackChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
            {
                label: 'Low',
                data: lowData,
                backgroundColor: 'rgba(251, 191, 36, 0.6)',
                borderColor:     'rgba(251, 191, 36, 1)',
                borderWidth: 1
            },
            {
                label: 'Critical',
                data: critData,
                backgroundColor: 'rgba(239, 68, 68, 0.6)',
                borderColor:     'rgba(239, 68, 68, 1)',
                borderWidth: 1
            }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,

            // NEW: stable interaction settings
            interaction: {
            mode: 'index',    // group by category (row)
            axis: 'y',        // use y-axis grouping
            intersect: true   // only show tooltip when you hover directly on the bar
            },

            plugins: {
            legend: { position: 'top' },
            tooltip: {
                // remove mode/intersect here; it's handled above
                callbacks: {
                title: items => items[0]?.label || '',
                label: ctx => `${ctx.dataset.label}: ${ctx.parsed.x}`
                }
            }
            },

            scales: {
            x: {
                stacked: true,
                beginAtZero: true,
                title: {
                display: true,
                text: 'SKU Count'
                }
            },
            y: {
                stacked: true
            }
            }
        }
    });
  </script>
@endsection
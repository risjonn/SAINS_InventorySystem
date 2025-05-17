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
          const allLabels    = @json($categoriesLabels);
          const allData      = @json($categoriesData);
          const lowThr       = @json($lowThresholdsJs);
          const critThr      = @json($criticalThresholdsJs);
          const divisionName = "{{ $division->name }}";
        
          // Filter & color logic remains the same
          const filtered = allData
            .map((v,i) => ({
              label: allLabels[i],
              value: v,
              low:   lowThr[i],
              crit:  critThr[i]
            }))
            .filter(x => x.value <= x.low);
        
          const labels       = filtered.map(x => x.label);
          const dataValues   = filtered.map(x => x.value);
          const bgColors     = filtered.map(x => x.value <= x.crit
                                ? 'rgba(220,38,38,0.7)'
                                : 'rgba(251,191,36,0.7)');
          const borderColors = filtered.map(x => x.value <= x.crit
                                ? 'rgba(220,38,38,1)'
                                : 'rgba(251,191,36,1)');
        
          const ctx = document.getElementById('lowStockChart').getContext('2d');
          new Chart(ctx, {
            type: 'bar',
            data: {
              labels,
              datasets: [{
                label: 'Stock Count',
                data: dataValues,
                backgroundColor: bgColors,
                borderColor: borderColors,
                borderWidth: 1,
                barPercentage: 0.6,
                categoryPercentage: 0.6
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
        
              // *** Better overall padding ***
              layout: {
                padding: {
                  top:    0,  // reduced from 100 so bars don't hug the bottom
                  right:  20,
                  bottom: 60,  // keeps long labels visible
                  left:   20
                }
              },
        
              scales: {
                x: {
                  grid: { display: false },
                  ticks: {
                    autoSkip:     false, // show every label
                    maxRotation:  0,
                    minRotation:  0,
                    padding:      10
                  }
                },
                y: {
                  beginAtZero: true,
                  ticks: {
                    stepSize: 1,
                    callback: v => Number.isInteger(v) ? v : ''
                  },
                  grid: { color: 'rgba(0,0,0,0.1)' }
                }
              },
        
              plugins: {
                // *** Add spacing below legend via legend.padding ***
                legend: {
                  display: true,
                  position: 'top',
                  padding: 30, 
                  labels: {
                    generateLabels: chart => [
                      { text: 'Low',      fillStyle: 'rgba(251,191,36,0.7)' },
                      { text: 'Critical', fillStyle: 'rgba(220,38,38,0.7)' }
                    ]
                  }
                },
        
                title: {
                  display: true,
                  text: [divisionName],
                  padding: { bottom: 0 }
                }
              }
            }
          });
        });
    </script>
@endsection
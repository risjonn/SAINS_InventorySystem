@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 my-5">
    <!-- Back to Dashboard Button -->
    <div class="my-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Dashboard
        </a>
    </div>

    <h2 class="text-2xl font-semibold mt-4">Low Stock Summary by Division</h2>

    <div class="mt-4 bg-white rounded-xl shadow-lg p-6">
    <table class="w-full mt-4 border-collapse">
        <thead>
            @php
                // flip asc↔︎desc
                $dirToggle = request('dir','asc') === 'asc' ? 'desc' : 'asc';
                $baseUrl   = url()->current();
            @endphp
            <tr class="bg-gray-200 text-left">
                <th class="p-3 border-b">
                    <a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->all(), ['sort'=>'name','dir'=>$dirToggle])) }}"
                        class="inline-flex items-center space-x-1">
                        <span>Division</span>
                        <i class="fas fa-sort{{ request('sort')==='name' 
                            ? (request('dir')==='asc' ? '-up' : '-down')
                            : '' }} ml-1"></i>
                    </a>
                </th>
                <th class="p-3 border-b text-center">
                    <a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->all(), ['sort'=>'categoriesAffected','dir'=>$dirToggle])) }}"
                        class="inline-flex items-center space-x-1">
                        <span>Affected Categories</span>
                        <i class="fas fa-sort{{ request('sort')==='categoriesAffected'
                            ? (request('dir')==='asc' ? '-up' : '-down')
                            : '' }} ml-1"></i>
                    </a>
                </th>
                <th class="p-3 border-b text-center">
                    <a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->all(), ['sort'=>'nonCriticalLow','dir'=>$dirToggle])) }}"
                        class="inline-flex items-center space-x-1">
                        <span>Low Stock</span>
                        <i class="fas fa-sort{{ request('sort')==='nonCriticalLow'
                            ? (request('dir')==='asc' ? '-up' : '-down')
                            : '' }} ml-1"></i>
                    </a>
                </th>
                <th class="p-3 border-b text-center">
                    <a href="{{ $baseUrl }}?{{ http_build_query(array_merge(request()->all(), ['sort'=>'critical','dir'=>$dirToggle])) }}"
                        class="inline-flex items-center space-x-1">
                        <span>Critical Stock</span>
                        <i class="fas fa-sort{{ request('sort')==='critical'
                            ? (request('dir')==='asc' ? '-up' : '-down')
                            : '' }} ml-1"></i>
                    </a>
                </th>
                <th class="p-3 border-b">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($divisionsSummary as $division)
                @php
                    $low  = $division['nonCriticalLow'];
                    $crit = $division['critical'];
                    $total = $low + $crit;
                @endphp
                <tr class="border-b odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-all duration-150">
                    <td class="p-3">{{ $division['name'] }}</td>

                    <td class="p-3 text-center">{{ $division['categoriesAffected'] }}</td>

                    <td class="p-3 text-center">
                        <span class="inline-flex items-center bg-amber-100 text-amber-800 px-3 py-1 rounded-full border border-amber-600 shadow-sm">
                        {{ $low }}
                        </span>
                    </td>

                    <td class="p-3 text-center">
                        <span class="inline-flex items-center bg-red-100 text-red-800 px-3 py-1 rounded-full border border-red-600 shadow-sm">
                        {{ $crit }}
                        </span>
                    </td>

                    <td class="p-3">
                        <a
                        href="{{ route('stocks.low', ['division' => $division['id']]) }}"
                        class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded transition-colors duration-200 text-sm"
                        >
                        <i class="fas fa-eye mr-1"></i>
                        View Details
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <!-- Chart Section: Horizontal bar of total flagged SKUs per division -->
    <div class="mt-8 bg-white rounded-xl shadow-lg p-6" style="max-width: auto; margin: auto; height: 400px;">
        <canvas id="divisionTotalChart" style="width:100%; height:100%;"></canvas>
    </div>

</div>
@endsection

@section('scripts')
  <!-- Chart.js from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Bring PHP data into JS
    const divisionsSummary = @json($divisionsSummary);
    const labels       = divisionsSummary.map(d => d.name);
    const totalData    = divisionsSummary.map(d => d.nonCriticalLow + d.critical);

    const ctx = document.getElementById('divisionTotalChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Flagged SKUs',
          data: totalData,
          backgroundColor: 'rgba(59, 130, 246, 0.6)',  // blue-500 at 60% opacity
          borderColor:     'rgba(59, 130, 246, 1)',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',            // horizontal bars
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => `${ctx.dataset.label}: ${ctx.parsed.x}`
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Flagged SKUs'
            }
          },
          y: {
            title: {
              display: false
            }
          }
        }
      }
    });
  </script>
@endsection
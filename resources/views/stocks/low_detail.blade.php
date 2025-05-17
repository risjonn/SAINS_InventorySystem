@extends('layouts.layout')

@section('title', "Low/Critical SKUs in {$category} - {$division->name}")

@section('content')
<div class="container mx-auto p-4">
  <div class="flex items-center justify-between mb-4">
    <div>
      <a href="{{ route('stocks.low', ['division' => $division->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150 mb-2">
        <i class="fas fa-arrow-left mr-2"></i>Summary By Category
      </a>
      <h1 class="text-2xl font-bold mb-1">Division: {{ $division->name }}</h1>
    </div>
  </div>

  {{-- Filter & Search --}}
  <form method="GET" class="flex flex-wrap items-center gap-4 mb-6">
    <input
      type="text"
      name="q"
      value="{{ request('q') }}"
      placeholder="Search product…"
      class="border rounded px-3 py-2 w-1/3"
    >

    <div class="relative">
      <select name="status" class="appearance-none bg-white border rounded px-3 py-2 pr-8">
        <option value="">All statuses</option>
        <option value="low"      {{ request('status')==='low'      ? 'selected':'' }}>Low</option>
        <option value="critical" {{ request('status')==='critical' ? 'selected':'' }}>Critical</option>
      </select>
    </div>

    <div class="relative">
      <select name="perPage" class="appearance-none bg-white border rounded px-3 py-2 pr-8">
        <option value="20" {{ request('perPage')==20 ? 'selected':'' }}>20 / page</option>
        <option value="30" {{ request('perPage')==30 ? 'selected':'' }}>30 / page</option>
        <option value="50" {{ request('perPage')==50 ? 'selected':'' }}>50 / page</option>
      </select>
    </div>

    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">Apply</button>
  </form>

  {{-- SKU Table --}}
  <div class="mt-4 bg-white rounded-xl shadow-lg p-6">
    <table class="table-auto w-full mb-6">
      <h2 class="text-xl font-semibold text-gray-600 mb-4">Category: {{ $category }}</h2>
      <thead class="bg-gray-100">
        <tr class="bg-gray-300">
          <th class="px-4 py-2 text-left">
            @php
              $isActive = request('sort') === 'name';
              $dir      = request('dir')  === 'asc' ? 'asc' : 'desc';
            @endphp
            <a href="?{{ http_build_query(array_merge(request()->all(), [
                  'sort' => 'name',
                  'dir'  => $isActive && $dir==='asc' ? 'desc' : 'asc'
            ])) }}"
              class="inline-flex items-center space-x-1">
              <span>Product</span>
              @if($isActive)
                <i class="fas fa-sort-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
              @else
                <i class="fas fa-sort text-gray-400"></i>
              @endif
            </a>
          </th>
          <th class="px-4 py-2 text-center">
            @php
              $isActive = request('sort') === 'qty';
              $dir      = request('dir')  === 'asc' ? 'asc' : 'desc';
            @endphp
            <a href="?{{ http_build_query(array_merge(request()->all(), [
                  'sort' => 'qty',
                  'dir'  => $isActive && $dir==='asc' ? 'desc' : 'asc'
            ])) }}"
              class="inline-flex items-center space-x-1">
              <span>Qty</span>
              @if($isActive)
                <i class="fas fa-sort-{{ $dir === 'asc' ? 'up' : 'down' }}"></i>
              @else
                <i class="fas fa-sort text-gray-400"></i>
              @endif
            </a>
          </th>
          <th class="px-4 py-2 text-center">Status</th>
          <th class="px-4 py-2 text-center">Low Thr.</th>
          <th class="px-4 py-2 text-center">Crit. Thr.</th>
        </tr>
      </thead>
      <tbody>
        @forelse($paginator as $row)
          @php
            $product = $row['product'];
          @endphp
          <tr class="bg-white even:bg-gray-100 hover:bg-gray-200">
            <td class="px-4 py-2 text-left">
              {{ $product->brand }} {{ $product->model }}
            </td>
            <td class="px-4 py-2 text-center">{{ $row['qty'] }}</td>
            <td class="px-4 py-2 text-center">
              @if($row['status'] === 'critical')
                <span class="inline-flex items-center bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">
                  <i class="fas fa-exclamation-triangle mr-1"></i> Critical
                </span>
              @elseif($row['status'] === 'low')
                <span class="inline-flex items-center bg-amber-100 text-amber-700 px-2 py-1 rounded-full text-xs font-semibold">
                  <i class="fas fa-exclamation-circle mr-1"></i> Low
                </span>
              @endif
            </td>
            <td class="px-4 py-2 text-center">{{ $row['low_threshold'] }}</td>
            <td class="px-4 py-2 text-center">{{ $row['critical_threshold'] }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              No SKUs match your criteria.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Pagination Links --}}
    <div class="mt-4">
      {{ $paginator->appends(request()->all())->links() }}
    </div>
    
  </div>
</div>
@endsection

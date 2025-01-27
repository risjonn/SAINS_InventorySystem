@extends('layouts.layout')

@section('title', 'Stock Inventory')

@section('content')
    <h1 class="text-center mb-4">Stock Inventory</h1>

    <div class="row mb-3 align-items-center">
        <!-- Add New Stock Button -->
        <div class="col-md-3">
            <a href="{{ route('stocks.create') }}" class="btn btn-primary w-100">+ Add New Stock</a>
        </div>

        <!-- Filter and Search -->
        <div class="col-md-9">
            <form action="{{ route('stocks.index') }}" method="GET" class="d-flex align-items-center">
                <!-- Filter by Category -->
                <select name="category" class="form-select me-2" style="max-width: 200px;" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ $current_category === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>

                <!-- Search Field -->
                <div class="position-relative flex-grow-1 me-2">
                    <input
                        type="text"
                        name="search"
                        id="search-box"
                        class="form-control"
                        placeholder="Search stocks... (Serial number, Brand, Model)"
                        autocomplete="off"
                    />
                    <ul
                        id="search-suggestions"
                        class="dropdown-menu w-100 position-absolute"
                        style="display: none; max-height: 200px; overflow-y: auto;"
                    ></ul>
                </div>
                <!-- Hidden input for serial number -->
                <input type="hidden" name="serial_number" id="search-hidden" value="">

                <!-- Search Button -->
                <button type="submit" class="btn btn-outline-secondary">Search</button>
            </form>
        </div>
    </div>    

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>
                    ID
                    <a href="{{ route('stocks.index', array_merge(request()->all(), ['sort_by' => 'id', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        @if (request('sort_by') === 'id')
                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>
                    Serial Number
                    <a href="{{ route('stocks.index', array_merge(request()->all(), ['sort_by' => 'serial_number', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        @if (request('sort_by') === 'serial_number')
                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>
                    Brand
                    <a href="{{ route('stocks.index', array_merge(request()->all(), ['sort_by' => 'brand', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        @if (request('sort_by') === 'brand')
                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>
                    Model
                    <a href="{{ route('stocks.index', array_merge(request()->all(), ['sort_by' => 'model', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        @if (request('sort_by') === 'model')
                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>
                    Category
                    <a href="{{ route('stocks.index', array_merge(request()->all(), ['sort_by' => 'category', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        @if (request('sort_by') === 'category')
                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>
                    Quantity
                    <a href="{{ route('stocks.index', array_merge(request()->all(), ['sort_by' => 'quantity', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        @if (request('sort_by') === 'quantity')
                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>
                    Updated At
                    <a href="{{ route('stocks.index', array_merge(request()->all(), ['sort_by' => 'updated_at', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}">
                        @if (request('sort_by') === 'updated_at')
                            <i class="fas fa-sort-{{ request('sort_direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $stock)
                <tr>
                    <td>{{ $stock->id }}</td>
                    <td>{{ $stock->serial_number }}</td>
                    <td>{{ $stock->brand }}</td>
                    <td>{{ $stock->model }}</td>
                    <td>{{ $stock->category }}</td>
                    <td>{{ $stock->quantity }}</td>
                    <td>{{ $stock->updated_at ? $stock->updated_at->format('d-m-Y H:i') : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('stocks.show', $stock) }}" class="btn btn-sm btn-info text-white">View</a>
                        <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-sm btn-warning text-white">Edit</a>
                        <button class="btn btn-sm btn-danger delete-button" data-action="{{ route('stocks.destroy', $stock) }}">Delete</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No stocks available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>   

    <div class="mt-3">
        {{ $stocks->links() }}
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchBox = document.getElementById('search-box');
            const suggestionsList = document.getElementById('search-suggestions');
            const hiddenInput = document.getElementById('search-hidden');

            searchBox.addEventListener('input', function () {
                const query = this.value.trim();

                if (query.length >= 1) {
                    const url = `{{ route('stocks.search') }}?query=${query}`;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            suggestionsList.innerHTML = ''; // Clear previous suggestions
                            if (data.length > 0) {
                                suggestionsList.style.display = 'block';
                                data.forEach(item => {
                                    const li = document.createElement('li');
                                    li.className = 'dropdown-item';
                                    li.textContent = `${item.brand} - ${item.model} (${item.serial_number})`;
                                    li.addEventListener('click', function () {
                                        searchBox.value = `${item.brand} - ${item.model} (${item.serial_number})`; // Set the input value
                                        hiddenInput.value = item.serial_number; // Set the hidden input
                                        suggestionsList.style.display = 'none'; // Hide suggestions
                                        document.querySelector('form').submit(); // Submit the form
                                    });
                                    suggestionsList.appendChild(li);
                                });
                            } else {
                                suggestionsList.style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Error fetching search suggestions:', error));
                } else {
                    suggestionsList.style.display = 'none';
                }
            });

            document.addEventListener('click', (e) => {
                if (!suggestionsList.contains(e.target) && e.target !== searchBox) {
                    suggestionsList.style.display = 'none';
                }
            });
        });
    </script>
@endsection

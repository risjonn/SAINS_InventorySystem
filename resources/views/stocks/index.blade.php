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
                <!-- 🔹 Section Dropdown for Filtering -->
                <select name="section" class="form-select me-2" style="max-width: 200px;" onchange="this.form.submit()">
                    <option value="">All Sections</option>
                    <optgroup label="Sarawak">
                        <option value="Kuching" {{ request('section', session('selectedSection')) == 'Kuching' ? 'selected' : '' }}>Kuching</option>
                        <option value="Sri Aman" {{ request('section', session('selectedSection')) == 'Sri Aman' ? 'selected' : '' }}>Sri Aman</option>
                        <option value="Betong" {{ request('section', session('selectedSection')) == 'Betong' ? 'selected' : '' }}>Betong</option>
                        <option value="Sarikei" {{ request('section', session('selectedSection')) == 'Sarikei' ? 'selected' : '' }}>Sarikei</option>
                        <option value="Sibu" {{ request('section', session('selectedSection')) == 'Sibu' ? 'selected' : '' }}>Sibu</option>
                        <option value="Mukah" {{ request('section', session('selectedSection')) == 'Mukah' ? 'selected' : '' }}>Mukah</option>
                        <option value="Kapit" {{ request('section', session('selectedSection')) == 'Kapit' ? 'selected' : '' }}>Kapit</option>
                        <option value="Bintulu" {{ request('section', session('selectedSection')) == 'Bintulu' ? 'selected' : '' }}>Bintulu</option>
                        <option value="Miri" {{ request('section', session('selectedSection')) == 'Miri' ? 'selected' : '' }}>Miri</option>
                        <option value="Limbang" {{ request('section', session('selectedSection')) == 'Limbang' ? 'selected' : '' }}>Limbang</option>
                    </optgroup>
                    <optgroup label="Sabah">
                        <option value="Sandakan" {{ request('section', session('selectedSection')) == 'Sandakan' ? 'selected' : '' }}>Sandakan</option>
                        <option value="Tawau" {{ request('section', session('selectedSection')) == 'Tawau' ? 'selected' : '' }}>Tawau</option>
                    </optgroup>
                </select>

                <!-- 🔹 Category Dropdown -->
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
                    <ul id="search-suggestions" class="dropdown-menu" style="max-height: 200px; overflow-y: auto;"></ul>
                </div>
                
                <!-- Hidden input for serial number -->
                <input type="hidden" name="serial_number" id="search-hidden" value="">

                <!-- Search Button -->
                <button type="submit" class="btn btn-outline-secondary">Search</button>
            </form>
        </div>
    </div>    

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Serial Number</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Section</th>
                <th>Updated At</th>
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
                    <td>{{ $stock->section }}</td>
                    <td>{{ $stock->updated_at ? $stock->updated_at->format('d-m-Y H:i') : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('stocks.show', $stock) }}" class="btn btn-sm btn-info text-white">View</a>
                        <a href="{{ route('stocks.edit', $stock) }}" class="btn btn-sm btn-warning text-white">Edit</a>
                        <form action="{{ route('stocks.destroy', $stock) }}" method="POST">
    @csrf
    @method('DELETE')
    <input type="hidden" name="section" value="{{ request('section', session('selectedSection', 'Kuching')) }}">
    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
</form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No stocks available for this section.</td>
                </tr>
            @endforelse
        </tbody>
    </table>   

    <div class="mt-3">
        {{ $stocks->links() }}
    </div>
@endsection

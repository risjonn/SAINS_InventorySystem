@extends('layouts.layout')

@section('title', isset($stock) ? 'Edit Stock' : 'Add New Stock')

@section('content')
    <h1 class="text-center mb-4">{{ isset($stock) ? 'Edit Stock' : 'Add New Stock' }}</h1>
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary mb-4">Back to List</a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($stock) ? route('stocks.update', $stock) : route('stocks.store') }}" method="POST" class="card p-4 shadow">
    @csrf
    @if (isset($stock))
        @method('PUT')
    @endif

    <!-- Add hidden field for section -->
    <input type="hidden" name="section" value="{{ session('selectedSection', 'Kuching') }}">

    <div class="mb-3">
        <label for="serial_number" class="form-label">Serial Number</label>
        <input type="text" id="serial_number" name="serial_number" class="form-control" value="{{ old('serial_number', $stock->serial_number ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="brand" class="form-label">Brand</label>
        <input type="text" id="brand" name="brand" class="form-control" value="{{ old('brand', $stock->brand ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="model" class="form-label">Model</label>
        <input type="text" id="model" name="model" class="form-control" value="{{ old('model', $stock->model ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select id="category" name="category" class="form-select" required>
            <option value="">Select Category</option>
            <option value="Router" {{ old('category', $stock->category ?? '') == 'Router' ? 'selected' : '' }}>Router</option>
            <option value="Switch" {{ old('category', $stock->category ?? '') == 'Switch' ? 'selected' : '' }}>Switch</option>
            <option value="Modem" {{ old('category', $stock->category ?? '') == 'Modem' ? 'selected' : '' }}>Modem</option>
            <option value="Access Points" {{ old('category', $stock->category ?? '') == 'Access Points' ? 'selected' : '' }}>Access Points</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity', $stock->quantity ?? '') }}" required>
    </div>
    <button type="submit" class="btn btn-primary">{{ isset($stock) ? 'Update Stock' : 'Add Stock' }}</button>
</form>

@endsection

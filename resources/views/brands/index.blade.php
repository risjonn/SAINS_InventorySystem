@extends('layouts.layout')

@section('title', 'Manage Brands')

@section('content')
<div class="container mx-auto px-4 my-5" x-data="{
    searchQuery: '{{ request('q') }}', 
    statusFilter: '{{ request('status') }}', 
    fetchBrands() {
        console.log('Fetching brands with search:', this.searchQuery, 'and status:', this.statusFilter);
        
        fetch('{{ route("brands.index") }}?q=' + this.searchQuery + '&status=' + this.statusFilter, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(data => {
                console.log('AJAX response data:', data);
                document.getElementById('brands_table').innerHTML = data;
            })
            .catch(error => console.error('Error fetching brands:', error));
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Brands</h2>
        <!-- Button to trigger Create Brand modal -->
        <button type="button" class="bg-green-500 hover:bg-green-600 transition-colors text-white px-5 py-3 rounded-md shadow"
        data-bs-toggle="modal" data-bs-target="#createBrandModal">
            <i class="fas fa-plus mr-1"></i> Add New Brand
        </button>
    </div>



    <!-- Search and Filter Card -->
    <div class="bg-white p-4 rounded-md shadow mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
            <!-- Search input -->
            <input type="text" id="search" placeholder="Search by brand name" 
                   class="w-full sm:w-1/3 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" 
                   x-model="searchQuery" x-on:input.debounce.300ms="fetchBrands">
            
            <!-- Status filter dropdown -->
            <select id="status_filter" class="w-full sm:w-1/4 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" 
                    x-model="statusFilter" x-on:change="fetchBrands">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <!-- Search button -->
            <button type="button" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 transition-colors text-white px-4 py-2 rounded-md shadow flex items-center justify-center"
                    @click="fetchBrands">
                <i class="fas fa-search mr-1"></i> Search
            </button>
        </div>
    </div>

    <!-- Brands Listing Table -->
    <div id="brands_table">
        @include('brands.partials.brand_table', ['brands' => $brands])
    </div>

    <form id="globalDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @include('brands.partials.create-modal')
    @include('brands.partials.edit-modal')
</div>
@endsection
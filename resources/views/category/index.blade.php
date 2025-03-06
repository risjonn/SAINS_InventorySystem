@extends('layouts.layout')

@section('title', 'Manage Categories')

@section('content')
<div class="container mx-auto px-4 my-5" x-data="{
    searchQuery: '{{ request('q') }}', 
    statusFilter: '{{ request('status') }}', 
    fetchCategories() {
        console.log('Fetching categories with search:', this.searchQuery, 'and status:', this.statusFilter);
        
        fetch('{{ route("category.index") }}?q=' + this.searchQuery + '&status=' + this.statusFilter, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(data => {
                console.log('AJAX response data:', data);
                document.getElementById('category_table').innerHTML = data;
            })
            .catch(error => console.error('Error fetching categories:', error));
    }
}">
    <h2 class="text-xl font-semibold mb-4">Manage Categories</h2>

    <!-- Button to trigger Create Category modal -->
    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded mb-4"
            data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="fas fa-plus mr-1"></i> Add New Category
    </button>

    <!-- Search and Filter Card -->
    <div class="bg-white p-4 rounded-md shadow mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <!-- Search input -->
            <input type="text" id="search" placeholder="Search by category name" 
                   class="w-full sm:w-1/3 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" 
                   x-model="searchQuery" x-on:input.debounce.300ms="fetchCategories">
            
            <!-- Status filter dropdown -->
            <select id="status_filter" class="w-full sm:w-1/4 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" 
                    x-model="statusFilter" x-on:change="fetchCategories">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <!-- Search button -->
            <button type="button" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 transition-colors text-white px-4 py-2 rounded-md shadow flex items-center justify-center"
                    @click="fetchCategories">
                <i class="fas fa-search mr-1"></i> Search
            </button>
        </div>
    </div>

    <!-- Category Listing Table -->
    <div id="category_table">
        @include('category.partials.category_table', ['category' => $categories])
    </div>

    <form id="globalDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @include('category.partials.create-modal')
    @include('category.partials.edit-modal')
</div>
@endsection
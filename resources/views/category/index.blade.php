@extends('layouts.layout')

@section('title', 'Manage Categories')

@section('content')
<div class="container mx-auto px-4 my-5" x-data="{
    searchQuery: '{{ request('q') }}',
    statusFilter: '{{ request('status') }}',
    sortBy: '{{ request('sort_by', 'name') }}',
    sortDirection: '{{ request('sort_direction', 'asc') }}',

    fetchCategories() {
        console.log('Fetching categories with search:', this.searchQuery, 'status:', this.statusFilter, 'sortBy:', this.sortBy, 'sortDirection:', this.sortDirection);
        
        fetch('{{ route("category.index") }}?' + new URLSearchParams({
            q: this.searchQuery,
            status: this.statusFilter,
            sort_by: this.sortBy,
            sort_direction: this.sortDirection
        }), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(data => {
                console.log('AJAX response data:', data);
                document.getElementById('category_tbody').innerHTML = data;
            })
            .catch(error => console.error('Error fetching categories:', error));
    },

    sortTable(column) {
        if (this.sortBy === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortBy = column;
            this.sortDirection = 'asc';
        }
        this.fetchCategories();
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Categories</h2>
        <!-- Button to trigger Create Brand modal -->
        <button type="button" class="bg-green-500 hover:bg-green-600 transition-colors text-white px-5 py-3 rounded-md shadow"
        data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus mr-1"></i> Add New Category
        </button>
    </div>

    <!-- Search and Filter Card -->
    <div class="bg-white p-4 rounded-md shadow mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
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
        <table class="min-w-full mt-4 border rounded-lg shadow-md bg-white">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-2 cursor-pointer" x-on:click="sortTable('name')">
                        <div class="flex items-center">
                            <span>Category Name</span>
                            <span class="flex flex-col items-center justify-center w-6 gap-0">
                                <i class="fas fa-sort-up m-0 leading-none -mb-1.5"
                                   :class="{'text-blue-700': sortBy === 'name' && sortDirection === 'asc', 'text-gray-400': !(sortBy === 'name' && sortDirection === 'asc')}"></i>
                                <i class="fas fa-sort-down m-0 leading-none -mt-1.5"
                                   :class="{'text-blue-700': sortBy === 'name' && sortDirection === 'desc', 'text-gray-400': !(sortBy === 'name' && sortDirection === 'desc')}"></i>
                            </span>
                        </div>
                    </th>
                    <th class="p-2 cursor-pointer" x-on:click="sortTable('active')">
                        <div class="flex items-center">
                            <span>Status</span>
                            <span class="flex flex-col items-center justify-center w-6 gap-0">
                                <i class="fas fa-sort-up m-0 leading-none -mb-1.5"
                                   :class="{'text-blue-700': sortBy === 'active' && sortDirection === 'asc', 'text-gray-400': !(sortBy === 'active' && sortDirection === 'asc')}"></i>
                                <i class="fas fa-sort-down m-0 leading-none -mt-1.5"
                                   :class="{'text-blue-700': sortBy === 'active' && sortDirection === 'desc', 'text-gray-400': !(sortBy === 'active' && sortDirection === 'desc')}"></i>
                            </span>
                        </div>
                    </th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody id="category_tbody">
                @include('category.partials.category_table_body', ['categories' => $categories])
            </tbody>
        </table>
        <div class="mt-4">
            {{ $categories->appends(request()->query())->links() }}
        </div>
    </div>

    <form id="globalDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @include('category.partials.create-modal')
    @include('category.partials.edit-modal')
</div>
@endsection
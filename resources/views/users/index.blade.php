@extends('layouts.layout')

@section('title', 'Manage Users')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{
    searchQuery: '{{ request('q') }}',
    divisionFilter: '{{ request('division_id') }}',
    roleFilter: '{{ request('role') }}',
    users: [],
    loading: false,
    currentPage: 1,
    totalPages: 1,
    showEditModal: false,
    currentUser: {
        id: null,
        username: '',
        name: '',
        email: '',
        division_id: '',
        role: ''
    },

    init() {
        this.fetchUsers();
    },

    async fetchUsers(page = 1) {
        this.loading = true;
        this.currentPage = page;

        try {
            const response = await fetch('{{ route("users.index") }}?' + new URLSearchParams({
                q: this.searchQuery,
                division_id: this.divisionFilter,
                role: this.roleFilter,
                page: this.currentPage
            }), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.text();
            document.getElementById('users_table').innerHTML = data;
            this.updatePaginationState();
        } catch (error) {
            console.error('Error fetching users:', error);
        } finally {
            this.loading = false;
        }
    },

    updatePaginationState() {
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const url = new URL(link.href);
                this.currentPage = url.searchParams.get('page') || 1;
                this.fetchUsers(this.currentPage);
            });
        });
    },

    openModal(userData) {
        this.currentUser = userData;
        this.showEditModal = true;
    },
    
    closeModal() {
        this.showEditModal = false;
    },
    
    deleteUser(url) {
        if (confirm('Are you sure you want to delete this user?')) {
            const form = document.getElementById('globalDeleteForm');
            form.action = url;
            form.submit();
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Users</h2>
        <a href="{{ route('users.create') }}" class="bg-green-500 hover:bg-green-600 transition-colors text-white px-5 py-3 rounded-md shadow">
            <i class="fas fa-user-plus mr-2"></i> Add New User
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-md shadow mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
            <input type="text" 
                   x-model="searchQuery" 
                   @input.debounce.300ms="fetchUsers()" 
                   placeholder="Search users..." 
                   class="w-full sm:w-1/3 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">

            <select x-model="divisionFilter" 
                    @change="fetchUsers()" 
                    class="w-full sm:w-1/4 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All Divisions</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                @endforeach
            </select>

            <select x-model="roleFilter" 
                    @change="fetchUsers()" 
                    class="w-full sm:w-1/4 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All Roles</option>
                <option value="Admin">Admin</option>
                <option value="Super Admin">Super Admin</option>
            </select>

            <button @click="fetchUsers()" 
                    class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 transition-colors text-white px-4 py-2 rounded-md shadow">
                <i class="fas fa-search mr-1"></i> Search
            </button>
        </div>
    </div>

    <!-- Table -->
    <div id="users_table">
        @include('users.partials.user_table', ['users' => $users])
    </div>

    <!-- Delete Form -->
    <form id="globalDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Edit Modal -->
    @include('users.partials.edit-modal')
</div>
@endsection
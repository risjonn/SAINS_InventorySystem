@extends('layouts.layout')

@section('title', 'Manage Users')

@section('content')
<div class="container mx-auto px-4 py-6"
     x-data="{
         activeTab: 'users',
         searchQuery: '{{ request('q') }}',
         divisionFilter: '{{ request('division_id') }}',
         roleFilter: '{{ request('role') }}',
         users: [],
         loading: false,
         currentPage: 1,
         totalPages: 1,
         showEditModal: false,
         currentUser: { id: null, username: '', name: '', email: '', division_id: '', role: '' },

         init() {
             this.fetchUsers();
         },

         async fetchUsers(page = 1) {
             this.loading = true;
             this.currentPage = page;

             try {
                 const response = await fetch('{{ route("users.index") }}?' +
                     new URLSearchParams({
                         q: this.searchQuery,
                         division_id: this.divisionFilter,
                         role: this.roleFilter,
                         page: this.currentPage
                     }), {
                         headers: { 'X-Requested-With': 'XMLHttpRequest' }
                     }
                 );

                 const html = await response.text();
                 document.getElementById('users_table').innerHTML = html;
                 this.updatePaginationState();
             } catch (error) {
                 console.error('Error fetching users:', error);
             } finally {
                 this.loading = false;
             }
         },

         updatePaginationState() {
             document.querySelectorAll('.pagination a').forEach(link => {
                 link.addEventListener('click', e => {
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
         },

         switchTab(tab) { this.activeTab = tab }
     }"
     x-init="init()"
>
    <!-- Header & Add-User Button -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Users</h2>
        <a href="{{ route('users.create') }}"
           class="bg-green-500 hover:bg-green-600 transition-colors text-white px-5 py-3 rounded-md shadow">
            <i class="fas fa-user-plus mr-2"></i> Add New User
        </a>
    </div>

    <!-- Tab Navigation -->
    <nav class="flex border-b mb-4">
        <button @click="switchTab('users')"
                :class="activeTab==='users' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-600'"
                class="px-4 py-2 border-b-2 font-medium">
            All Users
        </button>
        <button @click="switchTab('resetRequests')"
                :class="activeTab==='resetRequests' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-600'"
                class="px-4 py-2 border-b-2 font-medium relative">
            Password Reset Requests
            @if(count($resetRequests) > 0)
                <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    {{ count($resetRequests) }}
                </span>
            @endif
        </button>
        <button @click="activeTab = 'recoverRequests'"
            :class="activeTab==='recoverRequests' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-600'"
            class="px-4 py-2 border-b-2 font-medium relative">
            Password Recover Requests
            @if(count($recoverRequests) > 0)
                <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    {{ count($recoverRequests) }}
                </span>
            @endif
        </button>
    </nav>

    @if(session('status'))
        <div id="toastMessage" class="p-4 bg-green-100 text-green-800 rounded-t-lg">
            {{ session('status') }}
        </div>
    @endif

    <!-- All Users Tab -->
    <div x-show="activeTab==='users'" x-cloak>
        <!-- Filters -->
        <div class="bg-white p-4 rounded-md shadow mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
                <input type="text"
                       x-model="searchQuery"
                       @input.debounce.300ms="fetchUsers()"
                       placeholder="Search users"
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

        <!-- Users Table -->
        <div id="users_table">
            @include('users.partials.user_table', ['users' => $users])
        </div>
    </div>

    <!-- Password Reset Requests Tab -->
    <div x-show="activeTab==='resetRequests'" x-cloak class="space-y-4">
        <h3 class="text-xl font-semibold mb-2">Pending Password Reset Requests</h3>
        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
            <thead class="bg-gray-200 text-left">
                <tr>
                    <th class="p-3">Username</th>
                    <th class="p-3">Requested At</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resetRequests as $req)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $req->user->username }}</td>
                        <td class="p-3">{{ $req->created_at->format('j F, Y g:i') }}</td>
                        <td class="p-3">
                            <button
                                class="reset-button btn btn-sm btn-primary"
                                data-request="{{ $req->id }}"
                                data-username="{{ $req->user->username }}">
                                Reset Password
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500">
                            No pending requests.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Password Recover Requests -->
    <div x-show="activeTab === 'recoverRequests'" x-cloak class="space-y-4">
        <h3 class="text-xl font-semibold mb-2">Pending Password Recover Requests</h3>
        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
            <thead class="bg-gray-200 text-left">
                <tr>
                    <th class="p-3">Username</th>
                    <th class="p-3">Requested At</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recoverRequests as $request)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $request->user->username }}</td>
                        <td class="p-3">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.password.requests.updateRecover', $request->id) }}">
                                @csrf
                                <input type="hidden" name="request_id" value="{{ $request->id }}">
                                <button type="submit" class="btn btn-sm btn-primary">Recover</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500">
                            No pending requests.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        $(document).on('click', '.reset-button', function() {
            const reqId  = $(this).data('request');
            const usernm = $(this).data('username');

            // Populate & show the modal
            $('#reset-request-id').val(reqId);
            $('#reset-username').val(usernm);
            $('#new_password, #new_password_confirmation').val('');
            $('#resetPasswordModal').modal('show');
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toastMessage = document.getElementById('toastMessage');

            if (toastMessage) {
                // Set a timeout to hide the toast message after 5 seconds
                setTimeout(function() {
                    toastMessage.style.display = 'none';
                }, 3000);
            }
        });
    </script>

    <!-- Edit Modal -->
    @include('users.partials.edit-modal')
</div>
@endsection

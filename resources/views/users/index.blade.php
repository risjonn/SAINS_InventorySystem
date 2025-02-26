@extends('layouts.layout')

@section('title', 'Manage Users')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header with Title and Add New User Button -->
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manage Users</h2>
        <a href="{{ route('users.create') }}" class="bg-green-500 hover:bg-green-600 transition-colors text-white px-5 py-3 rounded-md shadow">
            <i class="fas fa-user-plus mr-2"></i> Add New User
        </a>
    </div>

    <!-- Search and Filter Card -->
    <div class="bg-white p-4 rounded-md shadow mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <input type="text" id="search" placeholder="Search by username, email, name or role" 
                   class="w-full sm:w-1/3 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400" value="{{ request('q') }}">
            <select id="division_filter" class="w-full sm:w-1/4 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All Divisions</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
            <select id="role_filter" class="w-full sm:w-1/4 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">All Roles</option>
                <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Super Admin" {{ request('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
            <button type="button" onclick="fetchUsers()" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 transition-colors text-white px-4 py-2 rounded-md shadow flex items-center justify-center">
                <i class="fas fa-search mr-1"></i> Search
            </button>
        </div>
    </div>

    <!-- User Table Container (loaded via AJAX) -->
    <div id="users_table">
        @include('users.partials.user_table', ['users' => $users])
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body space-y-4">
                        <div>
                            <label for="edit_username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="edit_username" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    required>
                        </div>
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="edit_name" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    required>
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="edit_email" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    required>
                        </div>
                        <div>
                            <label for="edit_division" class="block text-sm font-medium text-gray-700">Division</label>
                            <select name="division_id" id="edit_division" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    required>
                                <option value="">Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role" id="edit_role" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    required>
                                <option value="">Select Role</option>
                                <option value="Admin">Admin</option>
                                <option value="User Manager">User Manager</option>
                                <option value="Super Admin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer flex justify-end mt-4">
                        <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    function fetchUsers(){
        var search = $('#search').val();
        var division = $('#division_filter').val();
        var role = $('#role_filter').val();
        
        $.ajax({
           url: '{{ route("users.index") }}',
           type: 'GET',
           data: { 
             q: search,
             division_id: division,
             role: role 
           },
           success: function(data){
              $('#users_table').html(data);
           },
           error: function(xhr){
              console.error(xhr.responseText);
           }
        });
    }

    // Trigger live search when pressing Enter on the search field
    $('#search').on('keyup', function(e){
       if(e.keyCode === 13){
           fetchUsers();
       }
    });

    // Trigger filters on change
    $('#division_filter, #role_filter').on('change', function(){
       fetchUsers();
    });

    // Handle AJAX pagination links
    $(document).on('click', '#users_table .pagination a', function(e){
       e.preventDefault();
       var url = $(this).attr('href');
       $.ajax({
          url: url,
          success: function(data){
             $('#users_table').html(data);
          }
       });
    });

    // Use event delegation for edit buttons
    $(document).on('click', '.edit-user-btn', function() {
        var userId = $(this).data('id');
        var username = $(this).data('username');
        var name = $(this).data('name');
        var email = $(this).data('email');
        var divisionId = $(this).data('division');
        var role = $(this).data('role');

        // Set the modal form values
        $('#edit_username').val(username);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_division').val(divisionId);
        $('#edit_role').val(role);

        // Dynamically set the form action URL; assuming your update route is /users/{id}
        $('#editUserForm').attr('action', '/users/' + userId);
    });

    $(document).on('click', '.delete-button', function() {
        var action = $(this).data('action');
        $('#deleteForm').attr('action', action);
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handler for edit buttons
        document.querySelectorAll('.edit-user-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var userId = this.getAttribute('data-id');
                var username = this.getAttribute('data-username');
                var name = this.getAttribute('data-name');
                var email = this.getAttribute('data-email');
                var divisionId = this.getAttribute('data-division');
                var role = this.getAttribute('data-role');
    
                // Set the modal form values
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_division').value = divisionId;
                document.getElementById('edit_role').value = role;
    
                // Dynamically set the form action URL; assuming your update route is /users/{id}
                var form = document.getElementById('editUserForm');
                form.action = '/users/' + userId;
            });
        });
    });
</script>
@endsection

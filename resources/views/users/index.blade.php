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

    @include('users/partials.edit-modal')

    @include('users/partials.delete-confirmation-modal')
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

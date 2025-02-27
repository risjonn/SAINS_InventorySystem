<table class="min-w-full mt-4 border rounded-lg shadow-md bg-white mb-8">
    <thead>
        <tr class="bg-gray-200 text-left">
            <th class="p-2">Username</th>
            <th class="p-2">Name</th>
            <th class="p-2">Email</th>
            <th class="p-2">Division</th>
            <th class="p-2">Role</th>
            <th class="p-2">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr class="border-b hover:bg-gray-50">
            <td class="p-2">{{ $user->username }}</td>
            <td class="p-2">{{ $user->name }}</td>
            <td class="p-2">{{ $user->email }}</td>
            <td class="p-2">{{ $user->division->name ?? 'N/A' }}</td>
            <td class="p-2">{{ $user->role }}</td>
            <td class="p-2">
                <button type="button" 
                    class="edit-user-btn text-blue-500 hover:text-blue-700"
                    data-bs-toggle="modal" 
                    data-bs-target="#editUserModal"
                    data-id="{{ $user->id }}"
                    data-username="{{ $user->username }}"
                    data-name="{{ $user->name }}"
                    data-email="{{ $user->email }}"
                    data-division="{{ $user->division ? $user->division->id : '' }}"
                    data-role="{{ $user->role }}">
                    <i class="fas fa-edit"></i>
                </button>

                <button type="button" 
                        class="text-red-500 hover:text-red-700 delete-button"
                        data-action="{{ route('users.destroy', $user->id) }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $users->links() }}

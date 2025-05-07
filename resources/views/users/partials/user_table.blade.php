<table class="min-w-full mt-4 border rounded-lg shadow-md bg-white mb-8">  {{-- :contentReference[oaicite:0]{index=0}&#8203;:contentReference[oaicite:1]{index=1} --}}
    <thead>
        <tr class="bg-gray-200 text-left">
            <th class="p-2">Username</th>
            <th class="p-2">Name</th>
            {{-- <th class="p-2">Email</th> --}}
            <th class="p-2">Division</th>
            <th class="p-2">Role</th>
            <th class="p-2">Status</th>      {{-- New column --}}
            <th class="p-2">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr class="border-b hover:bg-gray-50">
            <td class="p-2">{{ $user->username }}</td>
            <td class="p-2">{{ $user->name }}</td>
            {{-- <td class="p-2">{{ $user->email }}</td> --}}
            <td class="p-2">{{ $user->division->name ?? 'N/A' }}</td>
            <td class="p-2">{{ $user->role }}</td>
            <td class="p-2">
                @if($user->deleted_at)
                    <span class="inline-block bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-sm italic">
                        Deleted
                    </span>
                @elseif($user->status === 'Active')
                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm font-semibold">
                        Active
                    </span>
                @else
                    <span class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm font-semibold">
                        Inactive
                    </span>
                @endif
            </td>
            <td class="p-2 space-x-2">
                <!-- Edit -->
                <button type="button" 
                        class="text-blue-500 hover:text-blue-700"
                        @click="openModal({
                            id: '{{ $user->id }}',
                            username: '{{ addslashes($user->username) }}',
                            name: '{{ addslashes($user->name) }}',
                            {{-- email: '{{ addslashes($user->email) }}', --}}
                            division_id: '{{ $user->division?->id }}',
                            role: '{{ addslashes($user->role) }}'
                        })"
                        title="Edit User">
                    <i class="fas fa-edit"></i>
                </button>

                @if(!$user->deleted_at)
                    @if($user->status === 'Active')
                        <button type="button"
                                class="status-button text-yellow-500 hover:text-yellow-700"
                                data-action="{{ route('users.deactivate', $user) }}"
                                data-type="user"
                                data-action-label="deactivate"
                                title="Deactivate User">
                        <i class="fas fa-user-slash"></i>
                        </button>
                    @else
                        <button type="button"
                                class="status-button text-green-500 hover:text-green-700"
                                data-action="{{ route('users.activate', $user) }}"
                                data-type="user"
                                data-action-label="activate"
                                title="Activate User">
                        <i class="fas fa-user-check"></i>
                        </button>
                    @endif
                @endif

                <!-- Delete -->
                <button type="button" 
                        class="text-red-500 hover:text-red-700 delete-button"
                        data-action="{{ route('users.destroy', $user->id) }}"
                        data-type="user"
                        title="Delete User">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($users->hasPages())
    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>
@endif

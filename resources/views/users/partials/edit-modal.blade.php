    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
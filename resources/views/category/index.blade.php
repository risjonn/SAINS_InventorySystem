@extends('layouts.layout')

@section('title', 'Manage Categories')

@section('content')
<div class="container mx-auto px-4 my-5">
    <h2 class="text-xl font-semibold mb-4">Manage Categories</h2>

    <!-- Button to trigger Create Category modal -->
    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded mb-4"
            data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="fas fa-plus mr-1"></i> Add New Category
    </button>

    <!-- Category Listing Table -->
    <table class="min-w-full mt-4 border rounded-lg shadow-md bg-white">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Category Name</th>
                <th class="p-2">Status</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr class="border-b">
                <td class="p-2">{{ $category->name }}</td>
                <td class="p-2">{{ $category->active ? 'Active' : 'Inactive' }}</td>
                <td class="p-2">
                    <!-- Edit Button with data attributes -->
                    <button type="button" class="text-blue-500 hover:text-blue-700 mr-2"
                        data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                        data-name="{{ $category->name }}"
                        data-active="{{ $category->active }}"
                        data-update-url="{{ route('category.update', $category->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <!-- Delete Button triggers the Delete Modal -->
                    <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                        data-action="{{ route('category.destroy', $category->id) }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('category.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="createCategoryModalLabel">Add New Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
           <div class="mb-3">
             <label for="categoryName" class="form-label">Category Name</label>
             <input type="text" class="form-control" id="categoryName" name="name" required>
           </div>
           <!-- Dropdown for status instead of a checkbox -->
           <div class="mb-3">
             <label for="categoryStatus" class="form-label">Status</label>
             <select class="form-select" id="categoryStatus" name="active">
               <option value="1">Active</option>
               <option value="0">Inactive</option>
             </select>
           </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Note: The form action is set dynamically -->
      <form id="editCategoryForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
           <div class="mb-3">
             <label for="editCategoryName" class="form-label">Category Name</label>
             <input type="text" class="form-control" id="editCategoryName" name="name" required>
           </div>
           <!-- Dropdown for status -->
           <div class="mb-3">
             <label for="editCategoryStatus" class="form-label">Status</label>
             <select class="form-select" id="editCategoryStatus" name="active">
               <option value="1">Active</option>
               <option value="0">Inactive</option>
             </select>
           </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  // Listen for the show event of the edit modal and populate it with data.
  var editCategoryModal = document.getElementById('editCategoryModal');
  editCategoryModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Button that triggered the modal
    var categoryName = button.getAttribute('data-name');
    var categoryActive = button.getAttribute('data-active');
    var updateUrl = button.getAttribute('data-update-url');

    // Update modal form fields
    var modal = this;
    modal.querySelector('#editCategoryName').value = categoryName;
    modal.querySelector('#editCategoryStatus').value = categoryActive; // Set dropdown value
    document.getElementById('editCategoryForm').action = updateUrl;
  });
</script>
@endsection

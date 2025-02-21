@extends('layouts.layout')

@section('title', 'Manage Brands')

@section('content')
<div class="container mx-auto px-4 my-5">
    <h2 class="text-xl font-semibold mb-4">Manage Brands</h2>

    <!-- Button to trigger Create Brand modal -->
    <button type="button" class="bg-green-600 text-white px-4 py-2 rounded mb-4"
            data-bs-toggle="modal" data-bs-target="#createBrandModal">
        <i class="fas fa-plus mr-1"></i> Add New Brand
    </button>

    <!-- Brands Listing Table -->
    <table class="min-w-full mt-4 border rounded-lg shadow-md bg-white">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Brand Name</th>
                <th class="p-2">Status</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($brands as $brand)
            <tr class="border-b">
                <td class="p-2">{{ $brand->name }}</td>
                <td class="p-2">{{ $brand->active ? 'Active' : 'Inactive' }}</td>
                <td class="p-2">
                    <!-- Edit Button with data attributes -->
                    <button type="button" class="text-blue-500 hover:text-blue-700 mr-2"
                        data-bs-toggle="modal" data-bs-target="#editBrandModal"
                        data-name="{{ $brand->name }}"
                        data-active="{{ $brand->active }}"
                        data-update-url="{{ route('brands.update', $brand->id) }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <!-- Delete Button triggers the Delete Modal -->
                    <button type="button" class="text-red-500 hover:text-red-700 delete-button"
                        data-action="{{ route('brands.destroy', $brand->id) }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $brands->links() }}
    </div>
</div>

<!-- Create Brand Modal -->
<div class="modal fade" id="createBrandModal" tabindex="-1" aria-labelledby="createBrandModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('brands.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="createBrandModalLabel">Add New Brand</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
           <div class="mb-3">
             <label for="brandName" class="form-label">Brand Name</label>
             <input type="text" class="form-control" id="brandName" name="name" required>
           </div>
           <!-- Dropdown for status -->
           <div class="mb-3">
             <label for="brandStatus" class="form-label">Status</label>
             <select class="form-select" id="brandStatus" name="active">
               <option value="1">Active</option>
               <option value="0">Inactive</option>
             </select>
           </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Brand</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" aria-labelledby="editBrandModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- The form action will be set dynamically -->
      <form id="editBrandForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editBrandModalLabel">Edit Brand</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
           <div class="mb-3">
             <label for="editBrandName" class="form-label">Brand Name</label>
             <input type="text" class="form-control" id="editBrandName" name="name" required>
           </div>
           <!-- Dropdown for status -->
           <div class="mb-3">
             <label for="editBrandStatus" class="form-label">Status</label>
             <select class="form-select" id="editBrandStatus" name="active">
               <option value="1">Active</option>
               <option value="0">Inactive</option>
             </select>
           </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Brand</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  // When the edit modal is shown, populate its fields with data from the triggering button.
  var editBrandModal = document.getElementById('editBrandModal');
  editBrandModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Button that triggered the modal
    var brandName = button.getAttribute('data-name');
    var brandActive = button.getAttribute('data-active');
    var updateUrl = button.getAttribute('data-update-url');

    // Populate the modal fields
    document.getElementById('editBrandName').value = brandName;
    document.getElementById('editBrandStatus').value = brandActive; // Sets the dropdown value

    // Set the form action URL dynamically
    document.getElementById('editBrandForm').action = updateUrl;
  });
</script>
@endsection

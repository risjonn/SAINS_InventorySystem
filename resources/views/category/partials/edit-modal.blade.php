<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- The form action will be set dynamically -->
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

@section('scripts')
<script>
  // When the edit category modal is shown, populate its fields with data.
  var editCategoryModal = document.getElementById('editCategoryModal');
  editCategoryModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Button that triggered the modal
    var categoryName = button.getAttribute('data-name');
    var categoryActive = button.getAttribute('data-active');
    var updateUrl = button.getAttribute('data-update-url');

    // Populate the modal fields
    document.getElementById('editCategoryName').value = categoryName;
    document.getElementById('editCategoryStatus').value = categoryActive; // Set the dropdown value

    // Set the form action URL dynamically
    document.getElementById('editCategoryForm').action = updateUrl;
  });
</script>
@endsection
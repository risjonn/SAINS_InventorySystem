<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- The form action will be set dynamically -->
      <form id="editCategoryForm" method="POST">
        @csrf
        <!-- This is where the issue is - we need the _method field for method spoofing -->
        <input type="hidden" name="_method" value="PUT">
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

          <!-- Bulk-item flag -->
          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              class="form-check-input" 
              id="editCategoryIsBulk" 
              name="is_bulk" 
              value="1"
            >
            <label class="form-check-label" for="editCategoryIsBulk">
              Track as bulk (no serial, use quantity)
            </label>
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
  var editCategoryModal = document.getElementById('editCategoryModal');
  editCategoryModal.addEventListener('show.bs.modal', function (event) {
    var button    = event.relatedTarget;           // the <button> that was clicked
    var name      = button.dataset.name;           // from data-name
    var active    = button.dataset.active;         // from data-active
    var isBulk    = button.dataset.isBulk === '1'; // from data-is-bulk
    var updateUrl = button.dataset.updateUrl;      // from data-update-url

    // Populate the fields
    document.getElementById('editCategoryName').value     = name;
    document.getElementById('editCategoryStatus').value   = active;
    document.getElementById('editCategoryIsBulk').checked = isBulk;

    // **Crucial**: set the form’s action to the PUT URL
    document.getElementById('editCategoryForm').action = updateUrl;
  });
</script>
@endsection
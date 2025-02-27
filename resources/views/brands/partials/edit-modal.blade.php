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
<!-- Edit Division Modal -->
<div class="modal fade" id="editDivisionModal" tabindex="-1" aria-labelledby="editDivisionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- The form action will be set dynamically -->
        <form id="editDivisionForm" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-header">
            <h5 class="modal-title" id="editDivisionModalLabel">Edit Division</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
             <div class="mb-3">
               <label for="editDivisionName" class="form-label">Division Name</label>
               <input type="text" class="form-control" id="editDivisionName" name="name" required>
             </div>
             <!-- Dropdown for status -->
             <div class="mb-3">
               <label for="editDivisionStatus" class="form-label">Status</label>
               <select class="form-select" id="editDivisionStatus" name="active">
                 <option value="1">Active</option>
                 <option value="0">Inactive</option>
               </select>
             </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Division</button>
          </div>
        </form>
      </div>
    </div>
</div>

@section('scripts')
<script>
  // When the edit division modal is shown, populate its fields with data.
  var editDivisionModal = document.getElementById('editDivisionModal');
  editDivisionModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Button that triggered the modal
    var divisionName = button.getAttribute('data-name');
    var divisionActive = button.getAttribute('data-active');
    var updateUrl = button.getAttribute('data-update-url');

    // Populate the modal fields
    document.getElementById('editDivisionName').value = divisionName;
    document.getElementById('editDivisionStatus').value = divisionActive; // Set the dropdown value

    // Set the form action URL dynamically
    document.getElementById('editDivisionForm').action = updateUrl;
  });
</script>
@endsection
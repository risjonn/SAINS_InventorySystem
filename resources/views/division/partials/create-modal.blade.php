<!-- Create Division Modal -->
<div class="modal fade" id="createDivisionModal" tabindex="-1" aria-labelledby="createDivisionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('division.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="createDivisionModalLabel">Add New Division</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
             <div class="mb-3">
               <label for="divisionName" class="form-label">Division Name</label>
               <input type="text" class="form-control" id="divisionName" name="name" required>
             </div>
             <!-- Dropdown for status -->
             <div class="mb-3">
               <label for="divisionStatus" class="form-label">Status</label>
               <select class="form-select" id="divisionStatus" name="active">
                 <option value="1">Active</option>
                 <option value="0">Inactive</option>
               </select>
             </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Division</button>
          </div>
        </form>
      </div>
    </div>
</div>
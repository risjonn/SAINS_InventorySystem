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
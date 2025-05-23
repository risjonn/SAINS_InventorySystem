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
             <!-- Dropdown for status -->
             <div class="mb-3">
               <label for="categoryStatus" class="form-label">Status</label>
               <select class="form-select" id="categoryStatus" name="active">
                 <option value="1">Active</option>
                 <option value="0">Inactive</option>
               </select>
             </div>
          </div>
             <!-- ↓ NEW: Bulk-item flag -->
          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              class="form-check-input" 
              id="createCategoryIsBulk" 
              name="is_bulk" 
              value="1" 
            >
            <label class="form-check-label" for="createCategoryIsBulk">
              Track as bulk (no serial, use quantity)
            </label>
          </div>
    
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <butt on type="submit" class="btn btn-primary">Save Category</button>
          </div>
        </form>
      </div>
    </div>
</div>
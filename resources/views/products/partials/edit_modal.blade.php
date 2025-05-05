<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editProductForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
           <div class="mb-3">
             <label for="editProductCode" class="form-label">Product Code</label>
             <input type="text" class="form-control" id="editProductCode" name="product_code" required>
           </div>
           <div class="mb-3">
             <label for="editCategory" class="form-label">Category</label>
             <select class="form-select" id="editCategory" name="category" required>
                  <option value="">Loading categories...</option>
             </select>
           </div>
           <div class="mb-3">
             <label for="editBrand" class="form-label">Brand</label>
             <input type="text" class="form-control" id="editBrand" name="brand" required>
           </div>
           <div class="mb-3">
             <label for="editModel" class="form-label">Model</label>
             <input type="text" class="form-control" id="editModel" name="model" required>
           </div>
           <div class="mb-3">
             <label for="editStatus" class="form-label">Status</label>
             <select class="form-select" id="editStatus" name="status">
               <option value="active">Active</option>
               <option value="inactive">Inactive</option>
             </select>
           </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editProductModal = document.getElementById('editProductModal');
        
        if (editProductModal) {
            editProductModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var productCode = button.getAttribute('data-product-code');
                var category = button.getAttribute('data-category');
                var brand = button.getAttribute('data-brand');
                var model = button.getAttribute('data-model');
                var status = button.getAttribute('data-status');
                var updateUrl = button.getAttribute('data-update-url');

                // Set form action
                document.getElementById('editProductForm').action = updateUrl;
                
                // Set form field values
                document.getElementById('editProductCode').value = productCode;
                document.getElementById('editBrand').value = brand;
                document.getElementById('editModel').value = model;
                document.getElementById('editStatus').value = status;
                
                // Reload categories and update the dropdown
                loadCategories().then(() => {
                    populateCategoryDropdown('editCategory', category);
                });
            });
        }
    });
</script>
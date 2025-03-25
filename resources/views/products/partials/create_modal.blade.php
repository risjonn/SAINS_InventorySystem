<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="createProductModalLabel">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
           <div class="mb-3">
             <label for="productCode" class="form-label">Product Code</label>
             <input type="text" class="form-control" id="productCode" name="product_code" required>
           </div>
           <div class="mb-3">
             <label for="category" class="form-label">Category</label>
             <select class="form-select" id="category" name="category" required>
               @foreach($activeCategories as $category)
                 <option value="{{ $category->name }}">{{ $category->name }}</option>
               @endforeach
             </select>
           </div>
           <div class="mb-3">
             <label for="brand" class="form-label">Brand</label>
             <input type="text" class="form-control" id="brand" name="brand" required>
           </div>
           <div class="mb-3">
             <label for="model" class="form-label">Model</label>
             <input type="text" class="form-control" id="model" name="model" required>
           </div>
           <div class="mb-3">
             <label for="status" class="form-label">Status</label>
             <select class="form-select" id="status" name="status" required>
               <option value="active">Active</option>
               <option value="inactive">Inactive</option>
             </select>
           </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

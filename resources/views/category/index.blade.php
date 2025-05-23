@extends('layouts.layout')

@section('title', 'Manage Categories')

@section('content')
<div class="container mx-auto px-4 my-5" x-data="{
    searchQuery: '{{ request('q') }}',
    statusFilter: '{{ request('status') }}',
    sortBy: '{{ request('sort_by', 'name') }}',
    sortDirection: '{{ request('sort_direction', 'asc') }}',

    fetchCategories() {
        fetch('{{ route("category.index") }}?' + new URLSearchParams({
            q: this.searchQuery,
            status: this.statusFilter,
            sort_by: this.sortBy,
            sort_direction: this.sortDirection
        }), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => document.getElementById('category_tbody').innerHTML = html);
    },

    sortTable(column) {
        if (this.sortBy === column) this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        else { this.sortBy = column; this.sortDirection = 'asc'; }
        this.fetchCategories();
    }
}">
    <!-- Back & Header -->
    <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Back to Products
    </a>
    <div class="flex flex-col sm:flex-row items-center justify-between my-6">
        <h2 class="text-2xl font-bold">Manage Categories</h2>
        <button type="button" class="bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded shadow"
                data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus mr-1"></i> Add New Category
        </button>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center">
            <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchCategories"
                   placeholder="Search by category name"
                   class="w-full sm:w-1/3 border rounded px-3 py-2" />

            <select x-model="statusFilter" @change="fetchCategories"
                    class="w-full sm:w-1/4 border rounded px-3 py-2">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <button type="button" @click="fetchCategories"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow flex items-center">
                <i class="fas fa-search mr-1"></i> Search
            </button>
        </div>
    </div>

    <!-- Categories Table -->
    <div id="category_table">
        <table class="min-w-full border rounded shadow bg-white">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 cursor-pointer" @click="sortTable('name')">
                        <div class="flex items-center">
                            <span>Category Name</span>
                            <span class="flex flex-col ml-2">
                                <i class="fas fa-sort-up"
                                   :class="{'text-blue-700': sortBy==='name'&&sortDirection==='asc','text-gray-400':!(sortBy==='name'&&sortDirection==='asc')}"></i>
                                <i class="fas fa-sort-down"
                                   :class="{'text-blue-700': sortBy==='name'&&sortDirection==='desc','text-gray-400':!(sortBy==='name'&&sortDirection==='desc')}"></i>
                            </span>
                        </div>
                    </th>
                    <th class="p-2 cursor-pointer" @click="sortTable('active')">
                        <div class="flex items-center">
                            <span>Status</span>
                            <span class="flex flex-col ml-2">
                                <i class="fas fa-sort-up"
                                   :class="{'text-blue-700': sortBy==='active'&&sortDirection==='asc','text-gray-400':!(sortBy==='active'&&sortDirection==='asc')}"></i>
                                <i class="fas fa-sort-down"
                                   :class="{'text-blue-700': sortBy==='active'&&sortDirection==='desc','text-gray-400':!(sortBy==='active'&&sortDirection==='desc')}"></i>
                            </span>
                        </div>
                    </th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody id="category_tbody">
                @include('category.partials.category_table_body', ['categories' => $categories])
            </tbody>
        </table>
        <div class="mt-4">
            {{ $categories->appends(request()->query())->links() }}
        </div>
    </div>

    <form id="globalDeleteForm" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

    {{-- Create Category Modal --}}
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('category.store') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Add New Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="active" class="form-select">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
              <div class="form-check mb-3">
                <input type="checkbox" name="is_bulk" value="1" class="form-check-input" id="createCategoryIsBulk">
                <label for="createCategoryIsBulk" class="form-check-label">
                  Track as bulk (no serial, use quantity)
                </label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Edit Category Modal --}}
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="editCategoryForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Edit Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input type="text" id="editCategoryName" name="name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select id="editCategoryStatus" name="active" class="form-select">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>
              <div class="form-check mb-3">
                <input type="checkbox" id="editCategoryIsBulk" name="is_bulk" value="1" class="form-check-input">
                <label for="editCategoryIsBulk" class="form-check-label">
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
</div>
@endsection

@section('scripts')
<script>
  var editCategoryModal = document.getElementById('editCategoryModal');
  editCategoryModal.addEventListener('show.bs.modal', function (event) {
    var button    = event.relatedTarget;
    var name      = button.dataset.name;
    var active    = button.dataset.active;
    var isBulk    = button.dataset.isBulk;           // ensure your table_body includes data-is-bulk
    var updateUrl = button.dataset.updateUrl;

    document.getElementById('editCategoryName').value     = name;
    document.getElementById('editCategoryStatus').value   = active;
    document.getElementById('editCategoryIsBulk').checked = (isBulk == '1');
    document.getElementById('editCategoryForm').action     = updateUrl;
  });
</script>
@endsection

<div class="modal fade" id="globalDeleteModal" tabindex="-1" role="dialog" aria-labelledby="globalDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <!-- Using a dedicated class for delete confirmation header -->
        <div class="modal-header delete-confirm">
          <!-- Animated delete icon -->
          <i class="fas fa-trash-alt delete-icon"></i>
          <h5 class="modal-title" id="globalDeleteModalLabel">Confirm Delete</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="deleteConfirmationMessage">Are you sure you want to delete this item?</p>
        </div>
        <div class="modal-footer">
          <button type="button" id="confirmDeleteButton" class="btn btn-danger">Delete</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
</div>
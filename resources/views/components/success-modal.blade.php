<div class="modal fade" id="globalSuccessModal" tabindex="-1" role="dialog" aria-labelledby="globalSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <!-- The header class will be set dynamically -->
        <div class="modal-header default" id="successModalHeader">
          <!-- Dynamic icon (if needed) -->
          <i class="fas success-icon" id="successModalIcon"></i>
          <h5 class="modal-title" id="globalSuccessModalLabel">{{ $title ?? 'Success' }}</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="globalSuccessModalBody">
          {{ $message ?? '' }}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
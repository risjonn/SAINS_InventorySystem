<!-- Reset-Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-blue-600 text-white">
          <h5 class="modal-title" id="resetPasswordModalLabel">Reset User Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="resetPasswordForm" method="POST" action="{{ route('admin.password.requests.update') }}">
          @csrf
          <div class="modal-body space-y-4">
            <input type="hidden" name="request_id" id="reset-request-id">
            <div>
              <label for="reset-username" class="block font-medium">Username</label>
              <input type="text" id="reset-username" class="w-full border rounded p-2 bg-gray-100" disabled>
            </div>
            <div>
              <label for="new_password" class="block font-medium">New Password</label>
              <input type="password" name="new_password" id="new_password" class="w-full border rounded p-2" required>
            </div>
            <div>
              <label for="new_password_confirmation" class="block font-medium">Confirm Password</label>
              <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="w-full border rounded p-2" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Reset & Notify</button>
          </div>
        </form>
      </div>
    </div>
</div>
  
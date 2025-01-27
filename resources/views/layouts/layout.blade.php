<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Vite Assets -->
    @vite(['resources/css/app.css'])
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <!-- Popup Message -->
        @if (session('success'))
            <div id="popup-message" class="alert alert-success alert-dismissible fade show shadow position-fixed" role="alert">
                <div class="d-flex flex-column align-items-center">
                    @if (session('action') === 'add')
                        <i class="fas fa-check-circle fa-3x text-success tick-animation"></i>
                    @elseif (session('action') === 'edit')
                        <i class="fas fa-edit fa-3x text-primary tick-animation"></i>
                    @elseif (session('action') === 'delete')
                        <i class="fas fa-trash fa-3x text-danger trashShake"></i>
                    @endif
                    <br>
                    <strong>{{ session('success') }}</strong>
                </div>
                <button type="button" class="btn-close mt-2" aria-label="Close"></button>
            </div>
            <div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark opacity-50" style="z-index: 1040;"></div>
        @endif

        @yield('content')
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Include Section for Page-Specific Scripts -->
    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Handle success popup message locking
            const popupMessage = document.getElementById('popup-message');
            const overlay = document.getElementById('overlay');
            if (popupMessage) {
                overlay.style.display = 'block';
                popupMessage.querySelector('.btn-close').addEventListener('click', () => {
                    popupMessage.remove();
                    overlay.remove();
                });
            }

            // Lock the delete confirmation modal to prevent closing via outside clicks or Escape key
            const deleteModalElement = document.getElementById('deleteModal');
            const deleteModal = new bootstrap.Modal(deleteModalElement, {
                backdrop: 'static', // Prevent closing by clicking outside
                keyboard: false     // Prevent closing by pressing Escape key
            });

            // Delete confirmation modal logic
            const deleteButtons = document.querySelectorAll('.delete-button');
            const deleteForm = document.getElementById('deleteForm');

            deleteButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const action = button.getAttribute('data-action');
                    deleteForm.setAttribute('action', action);
                    deleteModal.show();
                });
            });
        });
    </script>
</body>
</html>

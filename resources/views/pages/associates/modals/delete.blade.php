{{-- <div class="modal fade" id="delete-staff-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('process-delete-staff') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="account_id" id="delete_staff_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Confirm staff Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to delete this staff? This action cannot be undone.</p>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" name="password" id="delete_password" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete staff</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

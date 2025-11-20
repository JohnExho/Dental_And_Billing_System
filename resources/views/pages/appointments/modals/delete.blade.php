<div class="modal fade" id="delete-appointment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('process-delete-appointment') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="appointment_id" id="delete_appointment_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Confirm appointment Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to delete this appointment? This action cannot be undone.</p>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" name="password" id="delete_password" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete appointment</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModalEl = document.getElementById('delete-appointment-modal');

    deleteModalEl.addEventListener('hidden.bs.modal', function () {
        // Reset the form
        const form = deleteModalEl.querySelector('form');
        if (form) form.reset();

        // Clear hidden appointment_id
        const hiddenInput = deleteModalEl.querySelector('#delete_appointment_id');
        if (hiddenInput) hiddenInput.value = '';

        // Remove lingering backdrop if it exists (rare, but safe)
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });
});
</script>


<!-- Edit Progress Note Modal -->
<div class="modal fade" id="edit-progress-note-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-update-progress-note') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="note_id" id="edit_note_id">

                <!-- Header -->
                <div class="modal-header bg-gradient bg-warning text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i> Edit Progress Note
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_remarks" class="form-label">Remarks/Notes</label>
                            <textarea class="form-control" id="edit_remarks" name="remarks" rows="4" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Populate modal dynamically
    function openEditProgressNoteModal(noteId, noteText) {
        document.getElementById('edit_note_id').value = noteId;
        document.getElementById('edit_remarks').value = noteText;

        const modal = new bootstrap.Modal(document.getElementById('edit-progress-note-modal'));
        modal.show();
    }
</script>

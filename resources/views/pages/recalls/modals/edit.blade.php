<!-- Edit Recall Modal -->
<div class="modal fade" id="edit-recall-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-update-recall') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="recall_id" id="edit_recall_id">

                <!-- Header -->
                <div class="modal-header bg-gradient bg-warning text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i> Edit Recall
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="recall_reason" rows="4" required style="resize: none;"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>

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

    function _setSelectValueCaseInsensitive(selectEl, value) {
        if (!selectEl || value === null || value === undefined) return;
        const v = String(value);
        // Try direct set first
        try {
            selectEl.value = v;
            // If set succeeded and option exists, we're done
            if ([...selectEl.options].some(o => o.value === v)) return;
        } catch (e) {
            // ignore and fallback to manual matching
        }

        // Fallback: match case-insensitively
        const low = v.toLowerCase();
        for (let i = 0; i < selectEl.options.length; i++) {
            const opt = selectEl.options[i];
            if (String(opt.value).toLowerCase() === low) {
                selectEl.selectedIndex = i;
                return;
            }
        }
    }

    function openEditRecallModal(recallId, notes, status) {
        document.getElementById('edit_recall_id').value = recallId;
        document.getElementById('edit_notes').value = notes;

        const statusSelect = document.getElementById('edit_status');
        _setSelectValueCaseInsensitive(statusSelect, status);

        const modal = new bootstrap.Modal(document.getElementById('edit-recall-modal'));
        modal.show();
    }
</script>

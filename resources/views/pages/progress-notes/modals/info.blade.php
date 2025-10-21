<!-- Progress Note Info Modal -->
<div class="modal fade" id="info-progress-note-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- Header -->
            <div class="modal-header bg-gradient bg-primary text-white">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <i class="bi bi-journal-text me-2"></i> Progress Note Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Date</label>
                        <div id="info_note_date" class="fw-medium"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small text-muted">Author</label>
                        <div id="info_note_author" class="fw-medium"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small text-muted">Summary</label>
                        <div id="info_note_summary" class="fw-medium"></div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-muted">Remarks / Notes</label>
                        <div id="info_note_remarks" class="border rounded p-3 bg-white text-muted" style="min-height:120px; white-space:pre-wrap;"></div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer bg-light d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Populate and show the progress note info modal.
     * Call from buttons like:
     */
    function openProgressNoteInfoModal(noteId, summary, remarks, author, date) {
        document.getElementById('info_note_summary').textContent = summary ?? '-';
        document.getElementById('info_note_remarks').textContent = remarks ?? '-';
        document.getElementById('info_note_author').textContent = author ?? 'Unknown';
        document.getElementById('info_note_date').textContent = date ?? '-';

        const modal = new bootstrap.Modal(document.getElementById('info-progress-note-modal'));
        modal.show();
    }
</script>
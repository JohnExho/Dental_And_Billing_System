<!-- Edit Waitlist Modal -->
<div class="modal fade" id="edit-waitlist-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <form action="{{ route('process-update-waitlist') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="waitlist_id" id="edit_waitlist_id">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> Edit Waitlist
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-12">

                            <!-- Clinic (Locked / Display Only) -->
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-building me-1"></i> Clinic
                            </h6>
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control" value="{{ $wl->clinic->name }}" disabled>
                                <input type="hidden" name="clinic_id" value="{{ $wl->clinic_id }}">
                            </div>

                            <!-- Patient (Locked / Display Only) -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i> Patient
                                </label>
                                <div class="col-md-12 mb-3">
                                    <input type="text" class="form-control" value="{{ $wl->patient->full_name }}"
                                        disabled>
                                    <input type="hidden" name="patient_id" value="{{ $wl->patient_id }}">
                                </div>
                            </div>

                            <!-- Associate (Editable) -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-workspace me-1"></i> Associate
                                </label>
                                <select name="associate_id" class="form-select">
                                    <option value="">-- Select Associate --</option>
                                    <option value=""> Remove </option>
                                    @if (!empty($wl->associate))
                                        <option value="{{ $wl->associate?->associate_id }}"
                                            {{ $wl->associate_id == $wl->associate?->associate_id ? 'selected' : '' }}>
                                        </option>
                                    @else
                                        <option value="" disabled>
                                             'No Available Associate' 
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <!-- Status (Editable) -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check me-1"></i>Status
                                </label>
                                <select name="status" class="form-select">
                                    <option value="waiting" {{ $wl->status == 'waiting' ? 'selected' : '' }}>Waiting
                                    </option>
                                    <option value="in_consultation"
                                        {{ $wl->status == 'in_consultation' ? 'selected' : '' }}>In Consultation
                                    </option>
                                    <option value="completed" {{ $wl->status == 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save me-1"></i> Update Waitlist
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editWaitlistModal = document.getElementById('edit-waitlist-modal');

        editWaitlistModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            // Extract data attributes from the trigger button
            const associateId = button.getAttribute('data-associate_id');
            const laboratoryId = button.getAttribute('data-laboratory_id');
            const status = button.getAttribute('data-status');
            const waitlistId = button.getAttribute('data-id');
            document.getElementById('edit_waitlist_id').value = waitlistId;



            // Populate Associate
            const associateSelect = editWaitlistModal.querySelector('select[name="associate_id"]');
            if (associateSelect) {
                associateSelect.value = associateId || '';
            }

            // Populate Laboratory
            const laboratorySelect = editWaitlistModal.querySelector('select[name="laboratory_id"]');
            if (laboratorySelect) {
                laboratorySelect.value = laboratoryId || '';
            }

            // Populate Status
            const statusSelect = editWaitlistModal.querySelector('select[name="status"]');
            if (statusSelect) {
                statusSelect.value = status || 'waiting';
            }
        });
    });
</script>

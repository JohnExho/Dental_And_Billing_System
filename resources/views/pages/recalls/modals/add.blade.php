<!-- Add Recall Modal -->
<div class="modal fade" id="add-recall-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-recall') }}" method="POST">
                @csrf

                <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">

                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-calendar-event me-2"></i> Add Follow-up / Recall
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="followup_date" class="form-label fw-semibold">Follow-up / Recall Date</label>
                        <input type="date" class="form-control" id="followup_date" name="followup_date" required
                         value="{{ date('Y-m-d') }}"
                        min="{{ date('Y-m-d') }}" 
                        >
                    </div>

                    <div class="mb-3">
                        <label for="associate_id" class="form-label fw-semibold">Assign to Associate</label>
                        <select class="form-select" id="associate_id" name="associate_id" required>
                            <option value="" disabled selected>Select an associate</option>
                            @foreach ($associates as $associate)
                                <option value="{{ $associate->associate_id }}">{{ $associate->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="followup_reason" class="form-label fw-semibold">Reason</label>
                        <textarea class="form-control" id="followup_reason" name="followup_reason" rows="4" style="resize: none;"
                            placeholder="Enter reason..." required></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

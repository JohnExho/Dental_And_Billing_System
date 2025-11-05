<!-- Add Treatment Modal -->
<div class="modal fade" id="add-treatment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-treatment') }}" method="POST">
                @csrf

                <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">

                {{-- Header --}}
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-tools me-2"></i> Add Treatment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4">
                    {{-- Procedure --}}
                    <div class="mb-3">
                        <label for="procedure_id" class="form-label fw-semibold">Procedure</label>
                        <select id="procedure_id" name="procedure_id" class="form-select" required>
                            <option value="" disabled selected>Select Service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->service_id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tooth (checkboxes) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tooth (select one or more)</label>
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            @foreach($teeth as $tooth)
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="tooth_ids[]" 
                                        id="tooth_{{ $tooth->tooth_list_id }}" 
                                        value="{{ $tooth->tooth_list_id }}">
                                    <label class="form-check-label" for="tooth_{{ $tooth->tooth_list_id }}">
                                        {{ $tooth->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text">Select one or more teeth using the checkboxes above.</div>
                    </div>

                    {{-- Note --}}
                    <div class="mb-3">
                        <label for="treatment_note" class="form-label fw-semibold">Note</label>
                        <textarea id="treatment_note" name="note" class="form-control" rows="4" style="resize: none;"
                                  placeholder="Enter note..." required></textarea>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="treatment_status" class="form-label fw-semibold">Status</label>
                        <select id="treatment_status" name="status" class="form-select" required>
                            <option value="planned">Planned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Save Treatment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Focus procedure when modal shown --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var treatmentModal = document.getElementById('add-treatment-modal');
    if (treatmentModal) {
        treatmentModal.addEventListener('shown.bs.modal', function () {
            var proc = document.getElementById('procedure_id');
            if (proc) proc.focus();
        });
    }
});
</script>

<!-- Edit Treatment Modal -->
<div class="modal fade" id="edit-treatment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-update-treatment') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="treatment_id" id="edit_treatment_id">
                <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">

                {{-- Header --}}
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-tools me-2"></i> Edit Treatment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4">
                    {{-- Procedure --}}
                    <div class="mb-3">
                        <label for="edit_procedure_id" class="form-label fw-semibold">Procedure</label>
                        <select id="edit_procedure_id" name="procedure_id" class="form-select" required>
                            <option value="" disabled selected>Select Service</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->service_id }}" data-price="{{ $service->final_price }}">
                                    {{ $service->name }} - ₱{{ number_format($service->final_price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tooth (checkboxes) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tooth (select one or more)</label>
                        <input type="hidden" name="tooth_id[]" value="">

                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($teeth as $tooth)
                                <div class="form-check">
                                    <input class="form-check-input edit-tooth" type="checkbox" name="tooth_id[]"
                                        value="{{ $tooth->tooth_list_id }}" id="edit_tooth_{{ $loop->index }}"
                                        data-price="{{ $tooth->final_price }}">
                                    <label class="form-check-label" for="edit_tooth_{{ $loop->index }}">
                                        {{ $tooth->name }} - ₱{{ number_format($tooth->final_price, 2) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Net Cost --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Net Cost (₱)</label>
                        <input type="text" id="edit_treatment_net_cost" class="form-control fw-bold text-success"
                            value="0.00" readonly>
                        <input type="hidden" id="edit_treatment_net_cost_input" name="net_cost" value="0.00">
                    </div>

                    {{-- Note --}}
                    <div class="mb-3">
                        <label for="edit_treatment_note" class="form-label fw-semibold">Note</label>
                        <textarea id="edit_treatment_note" name="note" class="form-control" rows="4" style="resize: none;"
                            placeholder="Enter note..."></textarea>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="edit_treatment_status" class="form-label fw-semibold">Status</label>
                        <select id="edit_treatment_status" name="status" class="form-select" required>
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
                        <i class="bi bi-check-circle me-1"></i> Update Treatment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById("edit-treatment-modal");
        const procedureSelect = document.getElementById("edit_procedure_id");
        const netCostDisplay = document.getElementById("edit_treatment_net_cost");
        const netCostInput = document.getElementById("edit_treatment_net_cost_input");

        function updateEditNetCost() {
            const procedurePrice = parseFloat(procedureSelect.selectedOptions[0]?.dataset.price) || 0;

            let toothTotal = 0;
            document.querySelectorAll('.edit-tooth:checked').forEach(cb => {
                if (cb.checked) {
                    toothTotal += parseFloat(cb.dataset.price) || 0;
                }
            });

            const total = procedurePrice + toothTotal;
            netCostDisplay.value = total.toFixed(2);
            netCostInput.value = total.toFixed(2);
        }

        // Single event binding for all changes inside the modal
        modal.addEventListener("change", function(e) {
            if (e.target.matches("#edit_procedure_id, .edit-tooth")) {
                updateEditNetCost();
            }
        });

        // Function to populate edit modal
        window.populateEditTreatmentModal = function(treatment) {
            document.getElementById('edit_treatment_id').value = treatment.id;
            document.getElementById('edit_procedure_id').value = treatment.procedure_id;
            document.getElementById('edit_treatment_status').value = treatment.status;
            document.getElementById('edit_treatment_note').value = treatment.note || '';

            // Reset and set selected teeth
            document.querySelectorAll('.edit-tooth').forEach(cb => cb.checked = false);
            treatment.teeth.forEach(toothId => {
                const checkbox = document.querySelector(`.edit-tooth[value="${toothId}"]`);
                if (checkbox) checkbox.checked = true;
            });

            updateEditNetCost();
        };
    });
</script>

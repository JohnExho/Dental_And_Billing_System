<style>
/* Add spacing under the Service dropdown */
#service {
    margin-bottom: 22px !important;
}

/* Add spacing under the Net Cost input field */
#net_cost {
    margin-bottom: 26px !important;
}

/* Add extra spacing around the Remarks / Notes textarea */
#remarks {
    margin-top: 15px !important;
    margin-bottom: 30px !important;
}
</style>

<!-- Add progress-note Modal -->
<div class="modal fade" id="add-progress-note-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-progress-note') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">

                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-capsule me-2"></i> Add New Progress Note
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 !mb-2">
                            <label for="visit_date" class="form-label">Visit Date</label>
                            <input type="date" class="form-control" id="visit_date" value="{{ date('Y-m-d') }}" disabled>
                            <input type="hidden" name="visit_date" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6 !mb-2">
                            <label for="patient_name" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patient_name"
                                value="{{ $patient->full_name }}" disabled>
                        </div>

                        <div class="col-md-6 !mb-2">
                            <label for="followup_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="followup_date" name="followup_date" min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6 !mb-2">
                            <label for="followup_reason" class="form-label">Follow-up Reason</label>
                            <input type="text" class="form-control" id="followup_reason" name="followup_reason">
                        </div>

                        <!-- Service -->
                        <div class="col-md-6 !mb-1">
                            <label for="service" class="form-label">Service</label>
                            <select class="form-select" id="service" name="service">
                                <option selected disabled>Select Service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->service_id }}" data-price="{{ $service->final_price }}">
                                        {{ $service->name }} - ₱{{ number_format($service->final_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- FIXED SPACING — TOOTH SECTION -->
                        <div class="col-md-6 !mb-2">
                            <label for="tooth_id" class="form-label">Tooth (select one or more)</label>

                            <div id="tooth_list" class="border rounded"
                                style="max-height: 220px; overflow-y: auto; padding: 6px !important;">

                                @foreach ($teeth as $tooth)
                                    <div class="form-check mb-1" style="margin-bottom: 4px;">
                                        <input class="form-check-input" type="checkbox"
                                            name="tooth_id[]" value="{{ $tooth->tooth_list_id }}"
                                            id="tooth_{{ $loop->index }}"
                                            data-price="{{ $tooth->final_price }}">
                                        <label class="form-check-label" for="tooth_{{ $loop->index }}"
                                            style="font-size: 0.85rem;">
                                            {{ $tooth->name }} - ₱{{ number_format($tooth->final_price, 2) }}
                                        </label>
                                    </div>
                                @endforeach

                            </div>

                            <div class="form-text">Check one or more teeth.</div>
                        </div>

                        <!-- Net Cost -->
                        <div class="col-md-6 !mb-2">
                            <label class="form-label">Net Cost (₱)</label>
                            <input type="text" id="net_cost" class="form-control fw-bold text-success" value="0.00" readonly>
                            <input type="hidden" id="net_cost_input" name="net_cost" value="0.00">
                        </div>

                        <div class="col-12 !mb-2">
                            <label for="remarks" class="form-label">Remarks / Notes</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="4" style="resize: none;"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Logic (unchanged) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const serviceSelect = document.getElementById("service");
    const toothCheckboxSelector = 'input[name="tooth_id[]"]';
    const netCostDisplay = document.getElementById("net_cost");
    const netCostInput = document.getElementById("net_cost_input");
    const reasonField = document.getElementById("followup_reason");
    const dateField = document.getElementById("followup_date");

    function toggleFollowupRequirement() {
        if (reasonField.value.trim() !== "") {
            dateField.setAttribute("required", "required");
        } else {
            dateField.removeAttribute("required");
        }
    }
    reasonField.addEventListener("input", toggleFollowupRequirement);

    function updateNetCost() {
        const servicePrice = parseFloat(serviceSelect.selectedOptions[0]?.getAttribute("data-price")) || 0;
        let toothPrice = 0;
        const checked = document.querySelectorAll(`${toothCheckboxSelector}:checked`);
        checked.forEach(cb => {
            toothPrice += parseFloat(cb.getAttribute('data-price')) || 0;
        });
        const total = servicePrice + toothPrice;
        netCostDisplay.value = total.toFixed(2);
        netCostInput.value = total.toFixed(2);
    }

    serviceSelect.addEventListener("change", updateNetCost);
    document.querySelectorAll(toothCheckboxSelector).forEach(cb => cb.addEventListener('change', updateNetCost));

    updateNetCost();
});
</script>

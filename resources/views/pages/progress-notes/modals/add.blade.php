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
                        <div class="col-md-6">
                            <label for="visit_date" class="form-label">Visit Date</label>
                            <input type="date" class="form-control" id="visit_date" value="{{ date('Y-m-d') }}" disabled>
                            <input type="hidden" name="visit_date" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="patient_name" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patient_name"
                                value="{{ $patient->full_name }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <label for="followup_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="followup_date" name="followup_date" min="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="followup_reason" class="form-label">Follow-up Reason</label>
                            <input type="text" class="form-control" id="followup_reason" name="followup_reason">
                        </div>

                        <div class="col-md-6">
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

                        <div class="col-md-6">
                            <label for="tooth_id" class="form-label">Tooth</label>
                            <select name="tooth_id" id="tooth_id" class="form-select">
                                <option selected disabled value="">Select Tooth</option>
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->tooth_list_id }}" data-price="{{ $tooth->final_price }}">
                                        {{ $tooth->name }} - ₱{{ number_format($tooth->final_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 💰 Net Cost only -->
                        <div class="col-md-6">
                            <label class="form-label">Net Cost (₱)</label>
                            <input type="text" id="net_cost" class="form-control fw-bold text-success" value="0.00" readonly>
                            <input type="hidden" id="net_cost_input" name="net_cost" value="0.00">
                        </div>

                        <div class="col-12">
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

<!-- JS Logic -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const serviceSelect = document.getElementById("service");
    const toothSelect = document.getElementById("tooth_id");
    const netCostDisplay = document.getElementById("net_cost");
    const netCostInput = document.getElementById("net_cost_input");
    const reasonField = document.getElementById("followup_reason");
    const dateField = document.getElementById("followup_date");

    // Require date only if follow-up reason is filled
    function toggleFollowupRequirement() {
        if (reasonField.value.trim() !== "") {
            dateField.setAttribute("required", "required");
        } else {
            dateField.removeAttribute("required");
        }
    }
    reasonField.addEventListener("input", toggleFollowupRequirement);

    // Compute net cost = service + tooth
    function updateNetCost() {
        const servicePrice = parseFloat(serviceSelect.selectedOptions[0]?.getAttribute("data-price")) || 0;
        const toothPrice = parseFloat(toothSelect.selectedOptions[0]?.getAttribute("data-price")) || 0;

        const total = servicePrice + toothPrice;
        netCostDisplay.value = total.toFixed(2);
        netCostInput.value = total.toFixed(2);
    }

    serviceSelect.addEventListener("change", updateNetCost);
    toothSelect.addEventListener("change", updateNetCost);
});
</script>

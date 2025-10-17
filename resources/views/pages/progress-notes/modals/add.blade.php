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
                        <i class="bi bi-capsule me-2"></i> Add New progress-note
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="visit_date" class="form-label">Visit Date</label>
                            <input type="date" class="form-control" id="visit_date" name="visit_date"
                                value="{{ date('Y-m-d') }}" disabled>
                            <input type="hidden" name="visit_date" value="{{ date('Y-m-d') }}">

                        </div>
                        <div class="col-md-6">
                            <label for="patient_name" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name"
                                value="{{ $patient->full_name }}" required disabled>

                        </div>
                        <div class="col-md-6">
                            <label for="followup_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="followup_date" name="followup_date">
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
                            <label for="teeth" class="form-label">Teeth</label>
                            <select name="tooth_id" id="tooth_id" class="form-control">
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->tooth_id }}" data-price="{{ $tooth->final_price }}">
                                        {{ $tooth->name }} - ₱{{ number_format($tooth->final_price, 2) }}
                                    </option>
                                @endforeach
                            </select>


                        </div>
                        <div class="col-md-6">
                            <label for="discount_input" class="form-label">Discount (%)</label>
                            <input type="number" class="form-control" id="discount_input" name="discount"
                                min="0" max="100" step="0.01" value="0">
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2"><strong>Total Cost:</strong> <span id="total_cost">0.00</span></div>
                                <div class="mb-2"><strong>Discount(%):</strong> <span id="discount">0.00</span></div>
                                <div><strong>Net Cost:</strong> <span id="net_cost">0.00</span></div>
                                <input type="hidden" name="net_cost" id="net_cost_input" value="0.00">
                            </div>

                        </div>
                        <div class="col-12">
                            <label for="remarks" class="form-label">Remarks/Notes</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-secondary ">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success ">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const serviceSelect = document.getElementById("service");
        const toothSelect = document.getElementById("tooth_id");
        const discountInput = document.getElementById("discount_input");
        const totalCost = document.getElementById("total_cost");
        const discount = document.getElementById("discount");
        const netCost = document.getElementById("net_cost");
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

        function updateCosts() {
            const servicePrice = parseFloat(serviceSelect.selectedOptions[0]?.getAttribute("data-price")) || 0;
            const toothPrice = parseFloat(toothSelect.selectedOptions[0]?.getAttribute("data-price")) || 0;
            const discountValue = parseFloat(discountInput.value) || 0;

            const total = servicePrice + toothPrice;
            totalCost.textContent = total.toFixed(2);

            const discountAmount = total * (discountValue / 100);
            discount.textContent = discountAmount.toFixed(2);

            const net = total - discountAmount;
            netCost.textContent = net.toFixed(2);
            if (netCostInput) netCostInput.value = net.toFixed(2);
        }

        serviceSelect.addEventListener("change", updateCosts);
        toothSelect.addEventListener("change", updateCosts);
        discountInput.addEventListener("input", updateCosts);
    });
</script>

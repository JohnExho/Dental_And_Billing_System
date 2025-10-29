<!-- Add Prescription Modal -->
<div class="modal fade" id="add-prescription-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-prescription') }}" method="POST">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">

                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-prescription2 me-2"></i> Add Prescription
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Medicine Name -->
                        <div class="col-md-6">
                            <label for="medicine_id" class="form-label">Medicine <span class="text-danger">*</span></label>
                            <select name="medicine_id" id="medicine_id" class="form-select" required>
                                <option selected disabled value="">Select Medicine</option>
                                @foreach ($medicines as $medicine)
                                    @php
                                        $clinicMedicine = $medicine->medicineClinics->firstWhere('clinic_id', session('clinic_id'));
                                        $price = $clinicMedicine ? $clinicMedicine->price : $medicine->default_price;
                                        $stock = $clinicMedicine ? $clinicMedicine->stock : 0;
                                    @endphp
                                    <option value="{{ $medicine->medicine_id }}"
                                        data-cost="{{ $price }}"
                                        data-stock="{{ $stock }}"
                                        {{ $stock <= 0 ? 'disabled' : '' }}>
                                        {{ $medicine->name }}
                                        @if($stock <= 0)
                                            (Out of Stock)
                                        @else
                                            - ₱{{ number_format($price, 2) }} | Stock: {{ $stock }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stock Display -->
                        <div class="col-md-3">
                            <label class="form-label">Available Stock</label>
                            <input type="text" id="medicine_stock" class="form-control" readonly value="-">
                        </div>

                        <!-- Amount -->
                        <div class="col-md-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control" min="1" required disabled>
                        </div>

                        <!-- Cost -->
                        <div class="col-md-6">
                            <label class="form-label">Total Cost (₱) <span class="text-danger">*</span></label>
                            <input type="text" id="medicine_cost" name="medicine_cost"
                                   class="form-control fw-bold text-success" readonly required>
                        </div>

                        <!-- Tooth (Optional) -->
                        <div class="col-md-6">
                            <label for="tooth_id" class="form-label">Tooth (Optional)</label>
                            <select name="tooth_id" id="tooth_id" class="form-select">
                                <option selected value="">Select Tooth (if applicable)</option>
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->tooth_list_id }}">{{ $tooth->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dosage Instructions -->
                        <div class="col-12">
                            <label for="dosage_instructions" class="form-label">Dosage Instructions <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="dosage_instructions" name="dosage_instructions" rows="2" required></textarea>
                        </div>

                        <!-- Prescription Notes -->
                        <div class="col-12">
                            <label for="prescription_notes" class="form-label">Prescription Notes</label>
                            <textarea class="form-control" id="prescription_notes" name="prescription_notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Save Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Logic -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const medicineSelect = document.getElementById("medicine_id");
    const costInput = document.getElementById("medicine_cost");
    const stockDisplay = document.getElementById("medicine_stock");
    const amountInput = document.getElementById("amount");

    let unitCost = 0;
    let stock = 0;

    medicineSelect.addEventListener("change", function() {
        const selected = medicineSelect.selectedOptions[0];
        unitCost = parseFloat(selected.getAttribute("data-cost")) || 0;
        stock = parseInt(selected.getAttribute("data-stock")) || 0;

        stockDisplay.value = stock;
        amountInput.disabled = stock <= 0;
        amountInput.max = stock > 0 ? stock : 0;
        amountInput.value = stock > 0 ? 1 : "";
        costInput.value = (unitCost * (amountInput.value || 0)).toFixed(2);
    });

    amountInput.addEventListener("input", function() {
        const amount = parseInt(amountInput.value) || 0;
        if (amount > stock) {
            amountInput.value = stock;
        }
        costInput.value = (unitCost * (amountInput.value || 0)).toFixed(2);
    });
});
</script>

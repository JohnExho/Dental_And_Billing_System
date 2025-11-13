<!-- Edit Prescription Modal -->
<div class="modal fade" id="edit-prescription-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-update-prescription') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="prescription_id" id="edit_prescription_id">

                <!-- Header -->
                <div class="modal-header bg-gradient bg-warning text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i> Edit Prescription
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Medicine Name (locked for edit) -->
                        <div class="col-md-6">
                            <label for="edit_medicine_id" class="form-label">Medicine</label>
                            <select name="medicine_id" id="edit_medicine_id" class="form-select" disabled>
                                @foreach ($medicines as $medicine)
                                    @php
                                        $clinicMedicine = $medicine->medicineClinics->firstWhere('clinic_id', session('clinic_id'));
                                        $price = $clinicMedicine ? $clinicMedicine->price : $medicine->default_price;
                                        $stock = $clinicMedicine ? $clinicMedicine->stock : 0;
                                    @endphp
                                    <option value="{{ $medicine->medicine_id }}"
                                            data-cost="{{ $price }}"
                                            data-stock="{{ $stock }}">
                                        {{ $medicine->name }}
                                        - ₱{{ number_format($price, 2) }} | Stock: {{ $stock }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="prescribed">Prescribed</option>
                                <option value="purchased">Purchased</option>
                            </select>
                        </div>

                        <!-- Tooth -->
                        <div class="col-md-3">
                            <label for="edit_tooth_id" class="form-label">Tooth (Optional)</label>
                            <select name="tooth_list_id" id="edit_tooth_id" class="form-select">
                                <option value="">Select Tooth</option>
                                @foreach ($teeth as $tooth)
                                    <option value="{{ $tooth->tooth_list_id }}">{{ $tooth->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stock Display -->
                        <div class="col-md-3">
                            <label class="form-label">Available Stock</label>
                            <input type="text" id="edit_medicine_stock" class="form-control" readonly value="-">
                        </div>

                        <!-- Amount -->
                        <div class="col-md-3">
                            <label for="edit_amount_prescribed" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" id="edit_amount_prescribed" name="amount_prescribed" class="form-control" min="1" required>
                        </div>

                        <!-- Total Cost -->
                        <div class="col-md-6">
                            <label class="form-label">Total Cost (₱)</label>
                            <input type="text" id="edit_medicine_cost" name="medicine_cost" class="form-control fw-bold text-success" readonly required>
                        </div>

                        <!-- Dosage Instructions -->
                        <div class="col-12">
                            <label for="edit_dosage_instructions" class="form-label">Dosage Instructions <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_dosage_instructions" name="dosage_instructions" rows="2" required></textarea>
                        </div>

                        <!-- Prescription Notes -->
                        <div class="col-12">
                            <label for="edit_prescription_notes" class="form-label">Prescription Notes</label>
                            <textarea class="form-control" id="edit_prescription_notes" name="prescription_notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Logic -->
<script>
function openEditPrescription(prescription) {
    const modal = new bootstrap.Modal(document.getElementById('edit-prescription-modal'));

    const medSelect = document.getElementById('edit_medicine_id');
    const stockInput = document.getElementById('edit_medicine_stock');
    const amountInput = document.getElementById('edit_amount_prescribed');
    const costInput = document.getElementById('edit_medicine_cost');
    const dosageInput = document.getElementById('edit_dosage_instructions');
    const notesInput = document.getElementById('edit_prescription_notes');

    // Prefill fields
    document.getElementById('edit_prescription_id').value = prescription.prescription_id;
    document.getElementById('edit_status').value = prescription.status;
    document.getElementById('edit_tooth_id').value = prescription.tooth_list_id ?? "";
    medSelect.value = prescription.medicine_id;

    // Get medicine info
    const selectedOption = medSelect.selectedOptions[0];
    const unitCost = parseFloat(selectedOption.getAttribute('data-cost')) || 0;
    const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

    // Set stock and cost
    stockInput.value = stock;
    amountInput.value = prescription.amount_prescribed ?? 1;
    costInput.value = (unitCost * amountInput.value).toFixed(2);

    // Fill dosage and notes
    dosageInput.value = prescription.dosage_instructions ?? "";
    notesInput.value = prescription.prescription_notes ?? "";

    // Live update cost
    amountInput.addEventListener('input', function() {
        const amount = parseInt(amountInput.value) || 0;
        if (amount > stock) amountInput.value = stock;
        costInput.value = (unitCost * (amountInput.value || 0)).toFixed(2);
    });

    modal.show();
}
</script>

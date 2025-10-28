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
                        <i class="bi bi-capsule me-2"></i> Edit Prescription
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="prescribed">Prescribed</option>
                                <option value="purchased">Purchased</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditPrescription(prescriptionId) {
        document.getElementById('edit_prescription_id').value = prescriptionId;
        const modal = new bootstrap.Modal(document.getElementById('edit-prescription-modal'));
        modal.show();
    }
</script>

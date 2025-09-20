<!-- Edit Medicine Modal -->
<div class="modal fade" id="edit-medicine-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-update-medicine') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Header -->
                <div class="modal-header bg-gradient bg-secondary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i> Edit Medicine
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <input type="hidden" name="medicine_id" id="edit-medicine-id">

                    <h6 class="text-secondary fw-semibold mb-3 border-start ps-2">
                        <i class="bi bi-info-circle me-2"></i> Medicine Information
                    </h6>

                    <div class="row g-4">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="edit-medicine-name" class="form-label fw-semibold">Medicine Name</label>
                            <input type="text" id="edit-medicine-name" name="name"
                                class="form-control form-control-lg rounded-3 shadow-sm" required>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="edit-medicine-description" class="form-label fw-semibold">Description</label>
                            <input type="text" id="edit-medicine-description" name="description"
                                class="form-control form-control-lg rounded-3 shadow-sm">
                        </div>

                        <!-- Clinics -->
                        <div class="col-12">
                            <h6 class="fw-bold mt-3">Clinic Availability</h6>
                            @foreach ($clinics as $clinic)
                                <div class="row align-items-center mb-2 border rounded p-2 shadow-sm clinic-row"
                                     data-clinic-id="{{ $clinic->clinic_id }}">
                                    <div class="col-md-4">
                                        <input type="checkbox" 
                                               class="form-check-input me-2 clinic-checkbox"
                                               name="clinics[{{ $clinic->clinic_id }}][selected]" value="1"
                                               id="edit-clinic-{{ $clinic->clinic_id }}">
                                        <label for="edit-clinic-{{ $clinic->clinic_id }}" class="form-check-label">
                                            {{ $clinic->name }}
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.01" min='0'
                                               class="form-control form-control-sm clinic-price"
                                               name="clinics[{{ $clinic->clinic_id }}][price]" placeholder="0.00">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min ='0'
                                               class="form-control form-control-sm clinic-stock"
                                               name="clinics[{{ $clinic->clinic_id }}][stock]" placeholder="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i> Update Medicine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('edit-medicine-modal');

    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const medicineId = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const description = button.getAttribute('data-description');
        const clinics = JSON.parse(button.getAttribute('data-clinics') || '[]');

        // Fill basic fields
        editModal.querySelector('#edit-medicine-id').value = medicineId;
        editModal.querySelector('#edit-medicine-name').value = name;
        editModal.querySelector('#edit-medicine-description').value = description;

        // Reset all clinic rows
        editModal.querySelectorAll('.clinic-row').forEach(row => {
            row.querySelector('.clinic-price').value = '';
            row.querySelector('.clinic-stock').value = '';
            row.querySelector('.clinic-checkbox').checked = false;
        });

        // Populate from clinics JSON
        clinics.forEach(c => {
            const row = editModal.querySelector(`.clinic-row[data-clinic-id="${c.id}"]`);
            if (row) {
                row.querySelector('.clinic-price').value = c.price ?? '';
                row.querySelector('.clinic-stock').value = c.stock ?? '';
                row.querySelector('.clinic-checkbox').checked = true;
            }
        });
    });
});

</script>
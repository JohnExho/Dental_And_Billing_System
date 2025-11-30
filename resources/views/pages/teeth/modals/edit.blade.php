<!-- Edit Tooth Modal -->
<div class="modal fade" id="edit-tooth-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form id="edit-tooth-form" action="{{ route('process-update-tooth') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tooth_list_id" id="edit_tooth_id">

                <!-- Header -->
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Tooth
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <h6 class="text-muted fw-semibold mb-3">
                        <i class="bi bi-info-circle me-2"></i> Tooth Information
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Tooth Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_number" class="form-label">Tooth Number (1–32)</label>
                            <input type="number" class="form-control" id="edit_number" name="number" min="1"
                                max="32" step="1" inputmode="numeric">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_price" class="form-label">Tooth Price</label>
                            <input type="number" class="form-control" id="edit_price" name="price"
                                inputmode="decimal" step="0.01" min="0">
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update Tooth
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('edit-tooth-modal');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const clinicPrice = button.getAttribute('data-clinic_price');
            const defaultPrice = button.getAttribute('data-default_price');
            // Fill fields from data attributes
            editModal.querySelector('#edit_tooth_id').value = button.getAttribute('data-id') || '';
            editModal.querySelector('#edit_name').value = button.getAttribute('data-name') || '';
            editModal.querySelector('#edit_number').value = button.getAttribute('data-number') || '';


            // Prefer clinic price, fallback to default
            if (clinicPrice !== null) {
                editModal.querySelector('#edit_price').value = clinicPrice;
            } else {
                editModal.querySelector('#edit_price').value = defaultPrice || '';
            }

        });

        // Enforce numeric + 1–32 range
        editModal.addEventListener('shown.bs.modal', function() {
            const numberInput = editModal.querySelector('#edit_number');
            const priceInput = editModal.querySelector('#edit_price');
            if (numberInput) {
                numberInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, ''); // only digits
                    const val = parseInt(this.value, 10);
                    if (val < 1) this.value = 1;
                    if (val > 32) this.value = 32;
                });
            }
            if (priceInput) {
                priceInput.addEventListener('input', function() {
                    // Allow only numbers and a single decimal point
                    this.value = this.value.replace(/[^0-9.]/g, '');

                    // Ensure only one decimal point
                    const parts = this.value.split('.');
                    if (parts.length > 2) {
                        this.value = parts[0] + '.' + parts.slice(1).join('');
                    }
                });
            }
        });
    });
</script>

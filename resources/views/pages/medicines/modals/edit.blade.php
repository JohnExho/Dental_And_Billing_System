<style>
/* Modal Footer Buttons */
.modal-footer .btn {
        transition: 
        background 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out;
}

.modal-footer .btn-success {
        background: #1A73FF;
}
/* Hover: slightly darker blue */
.modal-footer .btn-success:hover {
        background: #1e3765;
        color: #FFFEF2;
        transform: translateY(-2px);   /* subtle lift */
        box-shadow: 0 6px 12px rgba(0,0,0,0.2); /* soft shadow */
}

/* Active/Click: lighter blue */
.modal-footer .btn-success:active {
        color: #FFFEF2;
        background: #0f3e73;
        transform: translateY(2px) scale(0.98); /* real press effect */
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}


/* Optional: Secondary button hover */
.modal-footer .btn-secondary:hover {
        background: #1e3rgb(112, 112, 112)
        color: #FFFEF2;
        transform: translateY(-2px);   /* subtle lift */
        box-shadow: 0 6px 12px rgba(0,0,0,0.2); /* soft shadow */
}

.modal-footer .btn-secondary:active {
        color: #FFFEF2;
        background: #0f3e73;
        transform: translateY(2px) scale(0.98); /* real press effect */
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
</style>

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
                        <div class="col-md-6">
                            <label for="edit-medicine-description" class="form-label fw-semibold">Description</label>
                            <input type="text" id="edit-medicine-description" name="description"
                                class="form-control form-control-lg rounded-3 shadow-sm">
                        </div>

                        <!-- Price -->
                        <div class="col-md-6 mt-3">
                            <label for="edit-medicine-price" class="form-label fw-semibold">
                                {{ session()->has('clinic_id') ? 'Clinic Price' : 'Default Price' }}
                            </label>
                            <input type="number" step="0.01" min="0" id="edit-medicine-price" name="price"
                                class="form-control form-control-lg rounded-3 shadow-sm" required>
                        </div>

                        <!-- Stock (Clinic only) -->
                        @if (session()->has('clinic_id'))
                            <div class="col-md-6 mt-3">
                                <label for="edit-medicine-stock" class="form-label fw-semibold">Stock</label>
                                <input type="number" min="0" id="edit-medicine-stock" name="stock"
                                    class="form-control form-control-lg rounded-3 shadow-sm">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-2" data-bs-dismiss="modal">
                        ✖️ Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-2">
                        💾 Update Medicine
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
            const defaultPrice = button.getAttribute('data-default_price');
            const clinicPrice = button.getAttribute('data-clinic_price');
            const stock = button.getAttribute('data-stock');



            editModal.querySelector('#edit-medicine-id').value = medicineId;
            editModal.querySelector('#edit-medicine-name').value = name;
            editModal.querySelector('#edit-medicine-description').value = description || '';

            // ✅ Set price directly
            editModal.querySelector('#edit-medicine-price').value =
                clinicPrice ?? defaultPrice ?? '';

            const stockInput = editModal.querySelector('#edit-medicine-stock');
            if (stockInput) {
                stockInput.value = stock ?? '';
            }
        });
    });
</script>

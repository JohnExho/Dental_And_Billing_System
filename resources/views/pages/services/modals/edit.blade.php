<!-- Edit Service Modal -->
<div class="modal fade" id="edit-service-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form id="edit-service-form" action="{{ route('process-update-service') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="service_id" id="edit_service_id">

                <!-- Header -->
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title d-flex align-items-center">
                        ✏️ Edit Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <h6 class="text-muted fw-semibold mb-3">
                        <i class="bi bi-info-circle me-2"></i> Service Information
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_type" class="form-label">Service Type</label>
                            <input type="text" class="form-control" id="edit_type" name="service_type" required>
                        </div>

                        <div class="col-md-12">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_price" class="form-label">Service Price</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="edit_price" name="price" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        ✖️ Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        💾 Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('edit-service-modal');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            editModal.querySelector('#edit_service_id').value =
                button.getAttribute('data-id') || '';
            editModal.querySelector('#edit_name').value =
                button.getAttribute('data-name') || '';
            editModal.querySelector('#edit_type').value =
                button.getAttribute('data-type') || '';
            editModal.querySelector('#edit_description').value =
                button.getAttribute('data-description') || '';
            editModal.querySelector('#edit_price').value =
                button.getAttribute('data-price') || '';
        });

        editModal.addEventListener('shown.bs.modal', function() {
            const priceInput = editModal.querySelector('#edit_price');
            if (priceInput) {
                priceInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9.]/g, '');
                    const parts = this.value.split('.');
                    if (parts.length > 2) {
                        this.value = parts[0] + '.' + parts.slice(1).join('');
                    }
                });
            }
        });
    });
</script>

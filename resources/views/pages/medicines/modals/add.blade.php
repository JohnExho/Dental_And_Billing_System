<style>
.btn {
    transition:
        background 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out;
}

.btn.btn-primary:hover {
    background: #1558a6;    
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.btn.btn-primary:active {
        color: #FFFEF2;
        background: #0f3e73;
        transform: translateY(2px) scale(0.98); /* real press effect */
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.btn.btn-outline-secondary {
    color: black;
}

.btn.btn-outline-secondary:hover {
    background: #6c757d;    
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.btn.btn-secondary.btn-sm:hover {
    background: #5c636a;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
</style>

<!-- Add Medicine Modal -->
<div class="modal fade" id="add-medicine-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-medicine') }}" method="POST">
                @csrf

                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-capsule me-2"></i> Add New Medicine
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <h6 class="text-secondary fw-semibold mb-3 border-start ps-2">
                        <i class="bi bi-info-circle me-2"></i> Medicine Information
                    </h6>

                    <div class="row g-4">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="medicine-name" class="form-label fw-semibold">Medicine Name</label>
                            <input type="text" id="medicine-name" name="name"
                                class="form-control form-control-lg rounded-3 shadow-sm" placeholder="e.g., Amoxicillin"
                                value="{{ old('name') }}" required>
                        </div>

                        <!-- Description -->
                        <div class="col-md-6">
                            <label for="medicine-description" class="form-label fw-semibold">Description</label>
                            <input type="text" id="medicine-description" name="description"
                                class="form-control form-control-lg rounded-3 shadow-sm"
                                placeholder="Short description..." value="{{ old('description') }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Price & Stock -->
                    <h6 class="text-secondary fw-semibold mb-3 border-start ps-2">
                        <i class="bi bi-cash-stack me-2"></i>
                        {{ session()->has('clinic_id') ? 'Clinic Price & Stock' : 'Default Price' }}
                    </h6>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Price (₱)</label>
                            <input type="number" step="0.01" min="0" name="price"
                                class="form-control form-control-lg rounded-3 shadow-sm" value="{{ old('price') }}"
                                required>
                        </div>

                        @if (session()->has('clinic_id'))
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Stock</label>
                                <input type="number" min="0" name="stock"
                                    class="form-control form-control-lg rounded-3 shadow-sm" value="{{ old('stock') }}"
                                    required>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Save Medicine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('add-medicine-modal');

        modal.addEventListener('shown.bs.modal', function() {
            const stockInput = modal.querySelector('#medicine-stock');
            const priceInput = modal.querySelector('#medicine-price');

            // Stock: only whole numbers, min 0, strip leading zeros
            if (stockInput) {
                stockInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');
                    let val = parseInt(this.value, 10);
                    if (isNaN(val) || val < 0) val = 0;
                    this.value = val.toString();
                });
            }

            // Price: allow decimals, min 0.00
            if (priceInput) {
                priceInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9.]/g, '');
                    if ((this.value.match(/\./g) || []).length > 1) {
                        this.value = this.value.substring(0, this.value.length - 1);
                    }
                    let val = parseFloat(this.value);
                    if (isNaN(val) || val < 0) val = 0;
                    this.value = val.toString();
                });
            }
        });
    });
</script>

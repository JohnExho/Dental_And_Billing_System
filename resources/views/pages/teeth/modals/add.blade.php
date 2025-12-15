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

<!-- Add Tooth Modal -->
<div class="modal fade" id="add-tooth-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-tooth') }}" method="POST">
                @csrf

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-emoji-smile me-2"></i> Add New Tooth
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <h6 class="text-muted fw-semibold mb-3">
                        <i class="bi bi-info-circle me-2"></i> Tooth Information
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tooth-name" class="form-label">Tooth Name</label>
                            <input type="text" id="tooth-name" name="name" class="form-control"
                                value="{{ old('tooth_name', $tooth->tooth_name ?? '') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="tooth-number" class="form-label">Tooth Number</label>
                            <input type="number" id="tooth-number" name="number" class="form-control"
                                value="{{ old('tooth_number', $tooth->tooth_number ?? '') }}" min="1"
                                max="32" step="1" inputmode="numeric">
                        </div>
                        <div class="col-md-3">
                            <label for="tooth-price" class="form-label">Tooth Price</label>
                            <input type="number" id="tooth-price" name="price" class="form-control"
                                value="{{ old('tooth_price', $tooth->tooth_price ?? '') }}" inputmode="numeric">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Tooth
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('add-tooth-modal');

        modal.addEventListener('shown.bs.modal', function() {
            const toothNumberInput = modal.querySelector('#tooth-number');
            const toothPriceInput = modal.querySelector('#tooth-price');

            if (toothNumberInput) {
                toothNumberInput.addEventListener('input', function() {
                    // Strip non-digits
                    this.value = this.value.replace(/\D/g, '');

                    // Enforce range 1–32
                    const val = parseInt(this.value, 10);
                    if (val < 1) this.value = 1;
                    if (val > 32) this.value = 32;
                });
            }

            if (toothPriceInput) {
                toothPriceInput.addEventListener('input', function() {
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

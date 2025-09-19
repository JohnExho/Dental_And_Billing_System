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
                                   class="form-control form-control-lg rounded-3 shadow-sm"
                                   placeholder="e.g., Amoxicillin"
                                   value="{{ old('name') }}" required>
                        </div>

                        <!-- Clinic -->
                        <div class="col-md-6">
                            <label for="clinic" class="form-label fw-semibold">Clinic</label>
                            <select name="clinic_id" id="clinic" class="form-select form-select-lg rounded-3 shadow-sm" required>
                                <option value="">-- Select Clinic --</option>
                                @foreach ($clinics as $clinic)
                                    <option value="{{ $clinic->clinic_id }}"
                                        {{ old('clinic_id', $associate->clinic_id ?? '') == $clinic->clinic_id ? 'selected' : '' }}>
                                        {{ $clinic->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="medicine-description" class="form-label fw-semibold">Description</label>
                            <textarea id="medicine-description" name="description"
                                      class="form-control rounded-3 shadow-sm"
                                      rows="3"
                                      placeholder="Short description...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Price -->
                        <div class="col-md-6">
                            <label for="medicine-price" class="form-label fw-semibold">Price</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light fw-bold">₱</span>
                                <input type="number" step="0.01" id="medicine-price" name="price"
                                       class="form-control"
                                       placeholder="0.00"
                                       value="{{ old('price') }}" required>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="col-md-6">
                            <label for="medicine-stock" class="form-label fw-semibold">Stock</label>
                            <input type="number" id="medicine-stock" name="stock"
                                   class="form-control form-control-lg rounded-3 shadow-sm"
                                   placeholder="0"
                                   value="{{ old('stock', 0) }}" min="0" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
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

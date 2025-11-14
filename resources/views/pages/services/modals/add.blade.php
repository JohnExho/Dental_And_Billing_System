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

<!-- Add Service Modal -->
<div class="modal fade" id="add-service-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-service') }}" method="POST">
                @csrf

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-emoji-smile me-2"></i> Add New Service
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <h6 class="text-muted fw-semibold mb-3">
                        <i class="bi bi-info-circle me-2"></i> Service Information
                    </h6>
                    <div class="row g-3">
                        <!-- Service Name -->
                        <div class="col-md-6">
                            <label for="service-name" class="form-label">Service Name</label>
                            <input type="text" id="service-name" name="name" class="form-control"
                                value="{{ old('name') }}" required>
                        </div>

                        <!-- Service Type -->
                        <div class="col-md-6">
                            <label for="service-type" class="form-label">Service Type</label>
                            <input type="text" id="service-type" name="service_type" class="form-control"
                                value="{{ old('service_type') }}" required>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="service-description" class="form-label">Description</label>
                            <textarea id="service-description" name="description" class="form-control" rows="2" style="resize: none;">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Clinic Price -->
                    <h6 class="text-muted fw-semibold mb-3">
                        <i class="bi bi-hospital me-2"></i> Clinic Price
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="clinic-price" class="form-label">Price</label>
                            <input type="number" step="0.01" min="0" name="price" id="clinic-price" class="form-control"
                                value="{{ old('price') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

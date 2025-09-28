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

                    <!-- Clinics -->
                    <h6 class="text-muted fw-semibold mb-3">
                        <i class="bi bi-hospital me-2"></i> Assign to Clinics
                    </h6>
                    <div class="row g-3">
                        @foreach ($clinics as $clinic)
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox"
                                        name="clinics[{{ $clinic->id }}][selected]" id="clinic-{{ $clinic->id }}">
                                    <label class="form-check-label" for="clinic-{{ $clinic->id }}">
                                        {{ $clinic->name }}
                                    </label>
                                </div>
                                <input type="number" step="0.01" min="0"
                                    name="clinics[{{ $clinic->id }}][price]" class="form-control ms-2"
                                    placeholder="Price">
                            </div>
                        @endforeach
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

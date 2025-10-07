<!-- Add progress-note Modal -->
<div class="modal fade" id="add-progress-note-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{--  --}}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-capsule me-2"></i> Add New progress-note
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="visit_date" class="form-label">Visit Date</label>
                            <input type="date" class="form-control" id="visit_date" name="visit_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="patient_name" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name" value="{{ $patient->full_name }}"
                         required>
                        </div>
                        <div class="col-md-6">
                            <label for="followup_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="followup_date" name="followup_date">
                        </div>
                        <div class="col-md-6">
                            <label for="followup_reason" class="form-label">Follow-up Reason</label>
                            <input type="text" class="form-control" id="followup_reason" name="followup_reason">
                        </div>
                        <div class="col-md-6">
                            <label for="service" class="form-label">Service</label>
                            <select class="form-select" id="service" name="service">
                                <option selected disabled>Select Service</option>
                                <!-- Add service options here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="teeth" class="form-label">Teeth</label>
                            <select class="form-select" id="teeth" name="teeth">
                                <option selected disabled>Select Teeth</option>
                                <!-- Add teeth options here -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" disabled>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <div class="mb-2"><strong>Total Cost:</strong> 0.00</div>
                                <div class="mb-2"><strong>Discount(%):</strong> 0.00</div>
                                <div><strong>Net Cost:</strong> 0.00</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="remarks" class="form-label">Remarks/Notes</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="attachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control" id="attachments" name="attachments">
                        </div>
                        <div class="col-12">
                            <label for="signature" class="form-label">Patient Signature</label>
                            <input type="text" class="form-control border-0 border-bottom border-warning" id="signature" name="signature" placeholder="Patient Signature">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">

                    <!-- Step Buttons -->
                    <div>
                        <button type="button" id="prevBtn" class="btn btn-secondary ">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </button>
                        <button type="submit" id="submitBtn" class="btn btn-success ">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

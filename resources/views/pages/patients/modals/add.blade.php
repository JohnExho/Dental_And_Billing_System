<!-- Add patient Modal -->
<div class="modal fade" id="add-patient-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-patient') }}" method="POST">
                @csrf

                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-capsule me-2"></i> Add New Patient
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <!-- STEP CONTAINER -->
                    <div id="step1" class="step-content">
                        <div class="row g-3">
                            <!-- Name Fields -->
                            <div class="col-md-4">
                                <label class="form-label">Last name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">First name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle name</label>
                                <input type="text" name="middle_name" class="form-control">
                            </div>

                            <!-- Gender and Birthdate -->
                            <div class="col-md-6">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="sex" class="form-select" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" class="form-control" required>
                            </div>

                            <!-- Mobile and Email -->
                            <!-- Mobile and Contact -->
                            <div class="col-md-6">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="mobile_no" class="form-control phone-number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact No.</label>
                                <input type="text" name="contact_no" class="form-control phone-number">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div id="step2" class="step-content d-none">
                        <div class="row g-3">
                            <!-- Civil Status -->
                            <div class="col-md-6">
                                <label class="form-label">Civil Status</label>
                                <select name="civil_status" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="widowed">Widowed</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="separated">Separated</option>
                                    <option value="annulled">Annulled</option>
                                </select>
                            </div>

                            <!-- Weight & Height -->
                            <div class="col-md-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="text" name="weight" class="form-control numeric-only">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Height (cm)</label>
                                <input type="text" name="height" class="form-control numeric-only">
                            </div>


                            <!-- School -->
                            <div class="col-md-6">
                                <label class="form-label">School</label>
                                <input type="text" name="school" class="form-control">
                            </div>

                            <!-- Referral -->
                            <div class="col-md-6">
                                <label class="form-label">Referred By</label>
                                <input type="text" name="referral" class="form-control">
                            </div>

                            <!-- Occupation & Company -->
                            <div class="col-md-6">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company</label>
                                <input type="text" name="company" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>

                    <!-- Step Buttons -->
                    <div>
                        <button type="button" id="prevBtn" class="btn btn-secondary d-none">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" id="nextBtn" class="btn btn-primary">
                            Next <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" id="submitBtn" class="btn btn-success d-none">
                            <i class="bi bi-upload me-1"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS for Step Logic -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');

        let currentStep = 1;

        function updateSteps() {
            if (currentStep === 1) {
                step1.classList.remove('d-none');
                step2.classList.add('d-none');
                prevBtn.classList.add('d-none');
                nextBtn.classList.remove('d-none');
                submitBtn.classList.add('d-none');
            } else if (currentStep === 2) {
                step1.classList.add('d-none');
                step2.classList.remove('d-none');
                prevBtn.classList.remove('d-none');
                nextBtn.classList.add('d-none');
                submitBtn.classList.remove('d-none');
            }
        }

        function validateStep1() {
            const requiredFields = step1.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }

                // Real-time validation feedback
                field.addEventListener('input', () => {
                    if (field.value.trim()) {
                        field.classList.remove('is-invalid');
                    }
                });
            });

            return isValid;
        }

        nextBtn.addEventListener('click', function() {
            if (currentStep === 1 && validateStep1()) {
                currentStep = 2;
                updateSteps();
            }
        });

        prevBtn.addEventListener('click', function() {
            currentStep = 1;
            updateSteps();
        });

        updateSteps(); // Initial state

        // --- Phone number validation ---
        const phoneInputs = document.querySelectorAll('.phone-number');

        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                this.value = value;
            });
        });
        // --- Numeric-only fields for weight & height ---
        const numericOnlyInputs = document.querySelectorAll('.numeric-only');

        numericOnlyInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');

                // Optional: limit to 1 decimal point
                const parts = this.value.split('.');
                if (parts.length > 2) {
                    this.value = parts[0] + '.' + parts[1];
                }
            });
        });
    });
    document.getElementById('add-patient-modal').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            const submitBtnVisible = !document.getElementById('submitBtn').classList.contains('d-none');
            if (!submitBtnVisible) {
                event.preventDefault();
            }
        }
    });
</script>

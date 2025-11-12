<!-- Add Patient Modal -->
@extends('layout')
@section('title', 'Register')

<!-- Mobile-Optimized Styles -->
<style>
    /* Mobile & Tablet Responsive Adjustments */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        .modal-content {
            border-radius: 1rem !important;
        }

        .modal-body {
            padding: 1rem !important;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .modal-header {
            padding: 0.75rem 1rem;
        }

        .modal-title {
            font-size: 1rem;
        }

        .modal-footer {
            padding: 0.75rem 1rem;
            flex-direction: column;
            gap: 0.5rem;
        }

        .modal-footer>div {
            width: 100%;
            display: flex;
            gap: 0.5rem;
        }

        .modal-footer button {
            flex: 1;
            font-size: 0.875rem;
            padding: 0.5rem;
        }

        .form-label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .form-control,
        .form-select {
            font-size: 0.875rem;
            padding: 0.5rem;
        }

        .row.g-3 {
            --bs-gutter-y: 0.75rem;
        }

        /* Stack all columns on mobile */
        .col-md-3,
        .col-md-4,
        .col-md-6,
        .col-md-8,
        .col-md-12 {
            width: 100%;
        }
    }

    /* Tablet Adjustments */
    @media (min-width: 769px) and (max-width: 1024px) {
        .modal-dialog {
            max-width: 90%;
        }

        .modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
        }
    }

    /* Small mobile devices */
    @media (max-width: 576px) {
        .modal-header h5 i {
            display: none;
            /* Hide icon on very small screens */
        }

        .btn i {
            margin: 0 !important;
        }

        .btn-text {
            display: none;
        }
    }
</style>

<div class="modal fade" id="add-patient-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-create-patient') }}" method="POST" enctype="multipart/form-data">
                @csrf


                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-capsule me-2"></i>
                        <span>Add New Patient</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <!-- STEP 1 -->
                    <div id="step1" class="step-content">
                        <div class="row g-3">
                            <!-- Name Fields -->
                            <div class="col-md-4 col-12">
                                <label class="form-label">Last name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label">First name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label">Middle name</label>
                                <input type="text" name="middle_name" class="form-control">
                            </div>

                            <!-- Gender and Birthdate -->
                            <div class="col-md-6 col-12">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="sex" class="form-select" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label">Birthdate <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_birth" class="form-control" required>
                            </div>

                            <!-- Contact -->
                            <div class="col-md-6 col-12">
                                <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_no" class="form-control phone-number"
                                    placeholder="09XXXXXXXXX" required>
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label">Contact No. <span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" class="form-control phone-number"
                                    placeholder="09XXXXXXXXX" required>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div id="step2" class="step-content d-none">
                        <div class="row g-3">
                            <!-- Civil Status -->
                            <div class="col-md-6 col-12">
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
                            <div class="col-md-3 col-6">
                                <label class="form-label">Weight (kg)</label>
                                <input type="text" name="weight" class="form-control numeric-only">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label">Height (cm)</label>
                                <input type="text" name="height" class="form-control numeric-only">
                            </div>

                            <!-- School -->
                            <div class="col-md-6 col-12">
                                <label class="form-label">School</label>
                                <input type="text" name="school" class="form-control">
                            </div>

                            <!-- Referral -->
                            <div class="col-md-6 col-12">
                                <label class="form-label">Referred By</label>
                                <input type="text" name="referral" class="form-control">
                            </div>

                            <!-- Occupation & Company -->
                            <div class="col-md-6 col-12">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control">
                            </div>
                            <div class="col-md-6 col-12">
                                <label class="form-label">Company</label>
                                <input type="text" name="company" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3 (Address) -->
                    <div id="step3" class="step-content d-none">
                        <div class="row g-3">
                            <h6 class="text-muted"><i class="bi bi-geo-alt me-1"></i> Address</h6>

                            <div class="col-md-4 col-12">
                                <input type="text" class="form-control" id="house_no" name="address[house_no]"
                                    placeholder="House No.">
                            </div>
                            <div class="col-md-8 col-12">
                                <input type="text" class="form-control" id="street" name="address[street]"
                                    placeholder="Street">
                            </div>

                            <div class="col-12">
                                <select id="patient-province-select" class="form-select" required>
                                    <option value="">-- Province --</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->province_id }}" data-id="{{ $province->id }}">
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="address[province_id]" id="patient-province-hidden">
                            </div>

                            <div class="col-12">
                                <select id="patient-city-select" class="form-select" disabled required>
                                    <option value="">-- City --</option>
                                </select>
                                <input type="hidden" name="address[city_id]" id="patient-city-hidden">
                            </div>

                            <div class="col-12">
                                <select id="patient-barangay-select" class="form-select" disabled required>
                                    <option value="">-- Barangay --</option>
                                </select>
                                <input type="hidden" name="address[barangay_id]" id="patient-barangay-hidden">
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="qr_id" value="{{ $qr_id }}">

                <!-- Footer -->
                <div class="modal-footer bg-light d-flex justify-content-end">
                    <!-- Step Buttons -->
                    <div>
                        <button type="button" id="prevBtn" class="btn btn-secondary d-none">
                            <i class="bi bi-arrow-left"></i>
                            <span class="btn-text ms-1">Back</span>
                        </button>
                        <button type="button" id="nextBtn" class="btn btn-primary">
                            <span class="btn-text me-1">Next</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                        <button type="submit" id="submitBtn" class="btn btn-success d-none">
                            <i class="bi bi-upload"></i>
                            <span class="btn-text ms-1">Upload</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date().toISOString().split("T")[0];
        document.querySelector('input[name="date_of_birth"]').max = today;
    });
</script>

<!-- JS for Step Logic -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const steps = [
            document.getElementById('step1'),
            document.getElementById('step2'),
            document.getElementById('step3')
        ];
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.querySelector('#add-patient-modal form');
        let currentStep = 0;

        function updateSteps() {
            steps.forEach((s, i) => s.classList.toggle('d-none', i !== currentStep));
            prevBtn.classList.toggle('d-none', currentStep === 0);
            nextBtn.classList.toggle('d-none', currentStep === steps.length - 1);
            submitBtn.classList.toggle('d-none', currentStep !== steps.length - 1);
        }

        function validateStep(stepIndex) {
            const fields = steps[stepIndex].querySelectorAll('input, select, textarea');
            let isValid = true;
            let missingField = null;

            fields.forEach(field => {
                if (field.hasAttribute('required') && !field.value.trim()) {
                    if (!missingField) missingField = field;
                    isValid = false;
                }

                if (field.classList.contains('phone-number') && field.value.trim().length !== 11) {
                    if (!missingField) missingField = field;
                    isValid = false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Information',
                    text: `Please fill out all required fields properly before continuing.`,
                    position: 'top',
                    timer: 3000,
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });

                if (missingField) missingField.focus();
            }

            return isValid;
        }

        form.addEventListener('submit', function(e) {
            const emailInput = form.querySelector('input[name="email"]');
            const phoneInputs = form.querySelectorAll('.phone-number');

            let phoneValid = true;
            phoneInputs.forEach(input => {
                if (input.value.length !== 11) phoneValid = false;
            });

            const emailPattern = /^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
            const emailValid = emailPattern.test(emailInput.value);

            if (!phoneValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Phone Number',
                    position: 'top',
                    timer: 3000,
                    text: 'Each phone number must be exactly 11 digits.',
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
                return;
            }

            if (!emailValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Email',
                    position: 'top',
                    timer: 3000,
                    text: 'Email must start with a letter and follow a valid format.',
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
                return;
            }
        });

        nextBtn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep++;
                updateSteps();
            }
        });

        prevBtn.addEventListener('click', function() {
            currentStep--;
            updateSteps();
        });

        updateSteps();

        // Phone Number Restriction
        document.querySelectorAll('.phone-number').forEach(input => {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (!value.startsWith('09')) {
                    value = '09' + value.replace(/^0+|^9+/, '');
                }
                this.value = value.slice(0, 11);
            });
        });

        // Numeric-only fields
        document.querySelectorAll('.numeric-only').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
                const parts = this.value.split('.');
                if (parts.length > 2) this.value = parts[0] + '.' + parts[1];
            });
        });

        // Address cascading
        const provinceSelect = document.getElementById('patient-province-select');
        const provinceHidden = document.getElementById('patient-province-hidden');
        const citySelect = document.getElementById('patient-city-select');
        const cityHidden = document.getElementById('patient-city-hidden');
        const barangaySelect = document.getElementById('patient-barangay-select');
        const barangayHidden = document.getElementById('patient-barangay-hidden');

        function resetSelects() {
            citySelect.innerHTML = '<option value="">-- Select City --</option>';
            citySelect.disabled = true;
            cityHidden.value = '';
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;
            barangayHidden.value = '';
        }

        provinceSelect.addEventListener('change', async function() {
            resetSelects();
            const selected = this.selectedOptions[0];
            provinceHidden.value = selected.dataset.id || '';
            if (!this.value) return;
            citySelect.innerHTML = '<option>Loading cities…</option>';
            try {
                const res = await fetch(`/locations/cities/${this.value}`);
                const data = await res.json();
                citySelect.innerHTML = '<option value="">-- Select City --</option>';
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.city_id;
                    opt.dataset.id = c.id;
                    opt.textContent = c.name;
                    citySelect.appendChild(opt);
                });
                citySelect.disabled = false;
            } catch (err) {
                citySelect.innerHTML = '<option>Error loading cities</option>';
            }
        });

        citySelect.addEventListener('change', async function() {
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;
            cityHidden.value = '';
            barangayHidden.value = '';
            const selectedCity = this.selectedOptions[0];
            cityHidden.value = selectedCity?.dataset.id || '';
            if (!this.value) return;
            barangaySelect.innerHTML = '<option>Loading barangays…</option>';
            try {
                const res = await fetch(`/locations/barangays/${this.value}`);
                const data = await res.json();
                barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                data.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.barangay_id;
                    opt.dataset.id = b.id;
                    opt.textContent = b.name;
                    barangaySelect.appendChild(opt);
                });
                barangaySelect.disabled = false;
            } catch (err) {
                barangaySelect.innerHTML = '<option>Error loading barangays</option>';
            }
        });

        barangaySelect.addEventListener('change', function() {
            barangayHidden.value = this.selectedOptions[0]?.dataset.id || '';
        });

        // Prevent accidental submit on Enter before final step
        document.getElementById('add-patient-modal').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const submitVisible = !submitBtn.classList.contains('d-none');
                if (!submitVisible) event.preventDefault();
            }
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = new bootstrap.Modal(document.getElementById('add-patient-modal'));
        modal.show();
    });
</script>

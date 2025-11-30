<style>
    /* Modal Footer Buttons */
    .modal-footer .btn {
        transition:
            background 0.4s ease-in-out,
            transform 0.4s ease-in-out,
            box-shadow 0.4s ease-in-out;
    }

    /* Hover: slightly darker blue */
    .modal-footer .btn-primary:hover {
        background: #1e3765;
        color: #FFFEF2;
        transform: translateY(-2px);
        /* subtle lift */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        /* soft shadow */
    }

    /* Active/Click: lighter blue */
    .modal-footer .btn-primary:active {
        color: #FFFEF2;
        background: #0f3e73;
        transform: translateY(2px) scale(0.98);
        /* real press effect */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }


    /* Optional: Secondary button hover */
    .modal-footer .btn-secondary:hover {
        background: #1e3rgb(112, 112, 112) color: #FFFEF2;
        transform: translateY(-2px);
        /* subtle lift */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        /* soft shadow */
    }

    .modal-footer .btn-secondary:active {
        color: #FFFEF2;
        background: #0f3e73;
        transform: translateY(2px) scale(0.98);
        /* real press effect */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }
</style>

<!-- Edit Patient Modal (3-step wizard, compact) -->

<div class="modal fade" id="edit-patient-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form id="edit-patient-form" action="{{ route('process-update-patient') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="patient_id" id="edit_patient_id">

                <!-- Header -->
                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i> Edit Patient
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-3">
                    <div class="row">
                        <!-- Profile image (always visible on left) -->
                        <div class="col-md-3 d-flex flex-column align-items-center mb-3 mb-md-0">
                            <img id="edit_profile_preview" src="" alt="Profile"
                                class="rounded-circle border mb-2 shadow-sm" width="110" height="110"
                                style="object-fit:cover;">

                            <input type="file" name="profile_picture" id="edit_profile_picture_input"
                                class="form-control form-control-sm mb-2" accept="image/*">

                            <!-- ✅ Remove Profile Picture Toggle -->
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" id="remove_profile_picture_toggle"
                                    name="remove_profile_picture">
                                <label class="form-check-label small text-muted" for="remove_profile_picture_toggle">
                                    Remove current profile
                                </label>
                            </div>

                            <small class="text-muted text-center">Preview only — saved on Update</small>
                        </div>

                        <!-- Steps container -->
                        <div class="col-md-9 border-start border-secondary">
                            <!-- STEP 1 -->
                            <div id="edit-step1" class="edit-step-content">
                                <div class="row g-3 align-items-center">
                                    <div class="col-12">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <label class="form-label">Last name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="last_name" id="edit_last_name"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">First name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="first_name" id="edit_first_name"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Middle name</label>
                                                <input type="text" name="middle_name" id="edit_middle_name"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Gender <span
                                                        class="text-danger">*</span></label>
                                                <select name="sex" id="edit_sex" class="form-select" required>
                                                    <option value="other" selected>-- Select --</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Birthdate <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="date_of_birth" id="edit_date_of_birth"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Mobile</label>
                                                <input type="text" name="mobile_no" id="edit_mobile_no"
                                                    class="form-control phone-number">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Contact No.</label>
                                                <input type="text" name="contact_no" id="edit_contact_no"
                                                    class="form-control phone-number">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" id="edit_email"
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2 -->
                            <div id="edit-step2" class="edit-step-content d-none">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Civil Status</label>
                                        <select name="civil_status" id="edit_civil_status" class="form-select">
                                            <option value="">-- Select --</option>
                                            <option value="single">Single</option>
                                            <option value="married">Married</option>
                                            <option value="widowed">Widowed</option>
                                            <option value="divorced">Divorced</option>
                                            <option value="separated">Separated</option>
                                            <option value="annulled">Annulled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Weight (kg)</label>
                                        <input type="text" name="weight" id="edit_weight"
                                            class="form-control numeric-only">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Height (cm)</label>
                                        <input type="text" name="height" id="edit_height"
                                            class="form-control numeric-only">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">School</label>
                                        <input type="text" name="school" id="edit_school" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Referred By</label>
                                        <input type="text" name="referral" id="edit_referral"
                                            class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Occupation</label>
                                        <input type="text" name="occupation" id="edit_occupation"
                                            class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Company</label>
                                        <input type="text" name="company" id="edit_company" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3 -->
                            <div id="edit-step3" class="edit-step-content d-none">
                                <div class="row g-3">
                                    <h6 class="text-muted"><i class="bi bi-geo-alt me-1"></i> Address</h6>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="edit_house_no"
                                            name="address[house_no]" placeholder="House No.">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="edit_street"
                                            name="address[street]" placeholder="Street">
                                    </div>
                                    <div class="col-md-12">
                                        <select id="edit_province_select" class="form-select" required>
                                            <option value="">-- Province --</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->province_id }}"
                                                    data-id="{{ $province->id }}">{{ $province->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="address[province_id]" id="edit_province_hidden">
                                    </div>
                                    <div class="col-md-12">
                                        <select id="edit_city_select" class="form-select" disabled required>
                                            <option value="">-- City --</option>
                                        </select>
                                        <input type="hidden" name="address[city_id]" id="edit_city_hidden">
                                    </div>
                                    <div class="col-md-12">
                                        <select id="edit_barangay_select" class="form-select" disabled required>
                                            <option value="">-- Barangay --</option>
                                        </select>
                                        <p class="text-secondary">Previous Barangay: <span id="barangay_label"
                                                class="form-text"></span></p>
                                        <input type="hidden" name="address[barangay_id]" id="edit_barangay_hidden">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <div>
                        <button type="button" id="edit_prevBtn" class="btn btn-secondary d-none">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" id="edit_nextBtn" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-1"></i>Next
                        </button>
                        <button type="submit" id="edit_submitBtn" class="btn btn-success d-none">
                            <i class="bi bi-upload me-1"></i> Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@php
    // ✅ Define your default profile image paths
    $defaultProfile = [
        'male' => asset('storage/defaults/male.png'),
        'female' => asset('storage/defaults/female.png'),
        'other' => asset('storage/defaults/other.png'),
    ];
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('edit-patient-modal');
        if (!modal) return;

        // Steps
        const steps = [
            modal.querySelector('#edit-step1'),
            modal.querySelector('#edit-step2'),
            modal.querySelector('#edit-step3')
        ];
        const nextBtn = modal.querySelector('#edit_nextBtn');
        const prevBtn = modal.querySelector('#edit_prevBtn');
        const submitBtn = modal.querySelector('#edit_submitBtn');
        let currentStep = 0;

        function updateSteps() {
            steps.forEach((s, i) => s.classList.toggle('d-none', i !== currentStep));
            prevBtn.classList.toggle('d-none', currentStep === 0);
            nextBtn.classList.toggle('d-none', currentStep === steps.length - 1);
            submitBtn.classList.toggle('d-none', currentStep !== steps.length - 1);
        }

        function validateStep(stepIndex) {
            const requiredFields = steps[stepIndex].querySelectorAll('[required]');
            let isValid = true;
            requiredFields.forEach(field => {
                if (!field.value || !String(field.value).trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            return isValid;
        }

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

        // --- Cascading selects elements ---
        const provinceSelect = modal.querySelector('#edit_province_select');
        const provinceHidden = modal.querySelector('#edit_province_hidden');
        const citySelect = modal.querySelector('#edit_city_select');
        const cityHidden = modal.querySelector('#edit_city_hidden');
        const barangaySelect = modal.querySelector('#edit_barangay_select');
        const barangayHidden = modal.querySelector('#edit_barangay_hidden');

        function resetCityBarangay() {
            citySelect.innerHTML = '<option value="">-- Select City --</option>';
            citySelect.disabled = true;
            cityHidden.value = '';
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;
            barangayHidden.value = '';
        }

        async function loadCities(provinceValue, selectedCityValue = null) {
            citySelect.disabled = true;
            citySelect.innerHTML = '<option>Loading cities…</option>';
            try {
                const res = await fetch(`/locations/cities/${provinceValue}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                citySelect.innerHTML = '<option value="">-- Select City --</option>';
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.city_id;
                    opt.dataset.id = c.id;
                    opt.textContent = c.name;
                    citySelect.appendChild(opt);
                });
                if (selectedCityValue) {
                    citySelect.value = selectedCityValue;
                    cityHidden.value = citySelect.selectedOptions[0]?.dataset.id || '';
                }
                citySelect.disabled = false;
            } catch (err) {
                console.error(err);
                citySelect.innerHTML = '<option value="">Error loading cities</option>';
            }
        }

  async function loadBarangays(cityId, selectedBarangayId = null) {
            barangaySelect.disabled = true;
            barangaySelect.innerHTML = '<option>Loading barangays…</option>';
            try {
                const res = await fetch(`/locations/barangays/${cityId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                // Debug: Check what the API actually returns
                console.log('Barangay API response:', data);

                barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                data.forEach(b => {
                    const opt = document.createElement('option');
                    // Yajra uses 'id' as primary key, not 'barangay_id'
                    opt.value = b.id; // Changed from b.barangay_id
                    opt.dataset.id = b.id;
                    opt.textContent = b.name;
                    barangaySelect.appendChild(opt);
                });
                if (selectedBarangayId) {
                    barangaySelect.value = selectedBarangayId;
                    barangayHidden.value = barangaySelect.selectedOptions[0]?.dataset.id || '';
                }
                barangaySelect.disabled = false;
            } catch (err) {
                console.error(err);
                barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
            }
        }

        provinceSelect.addEventListener('change', async function() {
            resetCityBarangay();
            const selected = this.selectedOptions[0];
            provinceHidden.value = selected?.dataset.id || '';
            if (!this.value) return;
            await loadCities(this.value);
        });

        citySelect.addEventListener('change', async function() {
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;
            cityHidden.value = this.selectedOptions[0]?.dataset.id || '';
            barangayHidden.value = '';
            if (!this.value) return;
            await loadBarangays(this.value);
        });

        barangaySelect.addEventListener('change', function() {
            barangayHidden.value = this.selectedOptions[0]?.dataset.id || '';
        });

        // --- Populate modal when opened ---
        modal.addEventListener('show.bs.modal', async function(event) {
            const button = event.relatedTarget;

            // Basic fields
            document.getElementById('edit_patient_id').value = button.getAttribute('data-id') || '';

            // If you have provided first/middle/last separately use them; else parse name
            const fullName = button.getAttribute('data-name') || '';
            // try to split "Last, First Middle" or "First Middle Last"
            if (fullName.includes(',')) {
                // assume "Last, First Middle"
                const [last, rest] = fullName.split(',', 2);
                document.getElementById('edit_last_name').value = last.trim();
                const parts = (rest || '').trim().split(' ');
                document.getElementById('edit_first_name').value = parts.shift() || '';
                document.getElementById('edit_middle_name').value = parts.join(' ') || '';
            } else {
                // fallback: try split by spaces: First Middle Last
                const parts = fullName.trim().split(' ');
                document.getElementById('edit_first_name').value = parts[0] || '';
                document.getElementById('edit_middle_name').value = parts.length > 2 ? parts.slice(
                    1, -1).join(' ') : (parts[1] || '');
                document.getElementById('edit_last_name').value = parts.length > 1 ? parts[parts
                    .length - 1] : '';
            }

            // other simple fields
            document.getElementById('edit_email').value = button.getAttribute('data-email') || '';
            // contact can be "mobile | contact" or whatever — split by '|'
            const contactRaw = button.getAttribute('data-contact') || '';
            const contactParts = contactRaw.split('|').map(p => p.trim());
            document.getElementById('edit_mobile_no').value = contactParts[0] || '';
            document.getElementById('edit_contact_no').value = contactParts[1] || '';

            document.getElementById('edit_date_of_birth').value = button.getAttribute(
                'data-date_of_birth') || '';
            document.getElementById('edit_sex').value = button.getAttribute('data-sex') || '';
            document.getElementById('edit_civil_status').value = button.getAttribute(
                'data-civil_status') || '';
            document.getElementById('edit_occupation').value = button.getAttribute(
                'data-occupation') || '';
            document.getElementById('edit_company').value = button.getAttribute('data-company') ||
                '';
            document.getElementById('edit_weight').value = button.getAttribute('data-weight') || '';
            document.getElementById('edit_height').value = button.getAttribute('data-height') || '';
            document.getElementById('edit_school').value = button.getAttribute('data-school') || '';
            document.getElementById('edit_referral').value = button.getAttribute('data-referral') ||
                '';
            modal.querySelector('#barangay_label').textContent = button.getAttribute(
                'data-barangay_name') || '';

            // Profile picture preview (show empty box if none)
            // Profile picture preview with gender-based defaults
            const sex = (button.getAttribute('data-sex') || '').toLowerCase();
            const profileUrl = button.getAttribute('data-profile_picture');
            modal.dataset.originalProfile = profileUrl;
            const preview = document.getElementById('edit_profile_preview');

            let profileSrc = profileUrl && profileUrl.trim() !== '' ? profileUrl : null;

            if (!profileSrc) {
                switch (sex) {
                    case 'male':
                        profileSrc = @json($defaultProfile['male']);
                        break;
                    case 'female':
                        profileSrc = @json($defaultProfile['female']);
                        break;
                    default:
                        profileSrc = @json($defaultProfile['other']);
                }
            }

            preview.src = profileSrc;


            // --- Handle "Remove Profile Picture" toggle ---
            const removeToggle = modal.querySelector('#remove_profile_picture_toggle');
            removeToggle.addEventListener('change', function() {
                const preview = modal.querySelector('#edit_profile_preview');
                const fileInput = modal.querySelector('#edit_profile_picture_input');
                const originalProfile = modal.dataset
                    .originalProfile; // we'll store this on modal open

                if (this.checked) {
                    // ✅ Reset preview to gender-based default
                    const sex = (modal.querySelector('#edit_sex').value || '')
                        .toLowerCase();
                    let defaultImg;

                    switch (sex) {
                        case 'male':
                            defaultImg = @json($defaultProfile['male']);
                            break;
                        case 'female':
                            defaultImg = @json($defaultProfile['female']);
                            break;
                        default:
                            defaultImg = @json($defaultProfile['other']);
                    }

                    preview.src = defaultImg;
                    fileInput.value = '';
                    fileInput.disabled = true;
                } else {
                    // 🔁 Revert to original profile image
                    fileInput.disabled = false;

                    const sex = @json($patient->sex);
                    const defaultProfile =
                        sex === 'male' ?
                        @json(asset('storage/defaults/male.png')) :
                        sex === 'female' ?
                        @json(asset('storage/defaults/female.png')) :
                        @json(asset('storage/defaults/other.png'));

                    preview.src = originalProfile || defaultProfile;
                }
            });


            // Address: attempt to use provided granular data attributes first
            const houseNo = button.getAttribute('data-house_no') || '';
            const street = button.getAttribute('data-street') || '';
            document.getElementById('edit_house_no').value = houseNo;
            document.getElementById('edit_street').value = street;

            const provId = button.getAttribute('data-province_id') || '';
            const cityId = button.getAttribute('data-city_id') || '';
            const barangayId = button.getAttribute('data-barangay_id') || '';

            // Pre-select province if provided
            if (provId) {
                provinceSelect.value = provId;
                provinceHidden.value = provinceSelect.selectedOptions[0]?.dataset.id || '';
                // load cities and select
                await loadCities(provId, cityId);
                if (cityId) {
                    await loadBarangays(cityId, barangayId);
                }
            } else {
                // If no granular attrs, try to parse `data-address` by commas
                const fullAddress = button.getAttribute('data-address') || '';
                if (fullAddress) {
                    // naive parse: assume last parts are city/province/barangay — not guaranteed
                    const parts = fullAddress.split(',').map(p => p.trim());
                    // pop values into hidden labels (we don't auto-select without ids)
                    // place full address into street input as fallback
                    document.getElementById('edit_street').value = parts.slice(0, parts.length - 3)
                        .join(', ') || street || '';
                }
            }

            // ensure selects reflect hidden ids if dataset id exists
            // If provinceSelect still has value '' but button provided province_name, try to match by option text
            if (!provinceSelect.value && button.getAttribute('data-province_name')) {
                const pname = button.getAttribute('data-province_name');
                const found = Array.from(provinceSelect.options).find(o => o.textContent.trim() ===
                    pname.trim());
                if (found) {
                    provinceSelect.value = found.value;
                    provinceHidden.value = found.dataset.id || '';
                    await loadCities(found.value, cityId);
                    if (cityId) await loadBarangays(cityId, barangayId);
                }
            }

            // reset wizard to first step on open
            currentStep = 0;
            updateSteps();
        });

        // Live preview of selected image (preview only; replacement on submit)
        const profileInput = modal.querySelector('#edit_profile_picture_input');
        profileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                modal.querySelector('#edit_profile_preview').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });

        // Prevent accidental submit on Enter before final step
        modal.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                const submitVisible = !submitBtn.classList.contains('d-none');
                if (!submitVisible) event.preventDefault();
            }
        });

    });
</script>

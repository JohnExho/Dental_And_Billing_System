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

<!-- Edit Clinic Modal -->
<div class="modal fade" id="edit-clinic-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="edit-clinic-form" action="{{ route('process-update-clinic') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="clinic_id" id="edit_clinic_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Clinic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_clinic_name" class="form-label">Clinic Name</label>
                                <input type="text" class="form-control" id="edit_clinic_name" name="name"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="edit_clinic_description" class="form-label">Short Description</label>
                                <input type="text" class="form-control" id="edit_clinic_description"
                                    name="description">
                            </div>

                            <!-- Contact -->
                            <h6 class="text-muted mt-3">☎️ Contact</h6>
                            <div class="column">
                                <div class="col-md-15 mb-3">
                                    <label for="edit_specialty" class="form-label">Specialty</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_specialty"
                                        name="specialty">
                                </div>
                                <div class="col-md-15 mb-3">
                                    <label for="edit_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_contact_no" class="form-label">Phone / Landline</label>
                                    <input type="text" class="form-control form-control-sm phone-number"
                                        id="edit_contact_no" name="contact_no" maxlength="11" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_mobile_no" class="form-label">Mobile No</label>
                                    <input type="text" class="form-control phone-number" id="edit_mobile_no"
                                        name="mobile_no" maxlength="11" required>
                                </div>
                            </div>

                            <!-- Address -->
                            <h6 class="text-muted mt-3">📍 Address</h6>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="edit_house_no" class="form-label">House No.</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_house_no"
                                        name="address[house_no]">
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label for="edit_street" class="form-label">Street</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_street"
                                        name="address[street]">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="edit_province_select" class="form-label">Province</label>
                                    <select id="edit_province_select" class="form-select" required>
                                        <option value="">-- Select Province --</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->province_id }}" data-id="{{ $province->id }}">
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="address[province_id]" id="edit_province_hidden">
                                    <span id="province_label" class="form-text"></span>
                                    <!-- This will show the selected province name -->
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="edit_city_select" class="form-label">City</label>
                                    <select id="edit_city_select" class="form-select" disabled required>
                                        <option value="">-- Select City --</option>
                                    </select>
                                    <input type="hidden" name="address[city_id]" id="edit_city_hidden">
                                    <span id="city_label" class="form-text"></span>
                                    <!-- This will show the selected city name -->
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="edit_barangay_select" class="form-label">Barangay</label>
                                    <select id="edit_barangay_select" class="form-select" disabled required>
                                        <option value="">-- Select Barangay --</option>
                                    </select>
                                    <input type="hidden" name="address[barangay_id]" id="edit_barangay_hidden">
                                    <span id="barangay_label" class="form-text"></span>
                                    <!-- This will show the selected barangay name -->
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Schedule -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_schedule_summary" class="form-label">Schedule Summary</label>
                                <input type="text" class="form-control" id="edit_schedule_summary"
                                    name="schedule_summary" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Weekly Schedule</label>
                                <div class="row">
                                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                        <div class="day-row col-12 mb-2">
                                            <!-- Hidden input ensures unchecked days are sent as 0 -->
                                            <input type="hidden" name="schedule[{{ $day }}][active]"
                                                value="0">

                                            <div class="form-check mb-1">
                                                <input class="form-check-input day-check" type="checkbox"
                                                    id="edit_{{ $day }}"
                                                    name="schedule[{{ $day }}][active]" value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="edit_{{ $day }}">{{ $day }}</label>
                                            </div>

                                            <div class="d-flex time-inputs d-none">
                                                <input type="time" class="form-control me-2"
                                                    name="schedule[{{ $day }}][start]">
                                                <input type="time" class="form-control"
                                                    name="schedule[{{ $day }}][end]">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">💾Update Clinic</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">✖️Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const modal = document.getElementById('edit-clinic-modal');
        if (!modal) return;

        const form = modal.querySelector('form');

        // --- Cascading selects ---
        const provinceSelect = modal.querySelector('#edit_province_select');
        const provinceHidden = modal.querySelector('#edit_province_hidden');
        const citySelect = modal.querySelector('#edit_city_select');
        const cityHidden = modal.querySelector('#edit_city_hidden');
        const barangaySelect = modal.querySelector('#edit_barangay_select');
        const barangayHidden = modal.querySelector('#edit_barangay_hidden');

        function resetSelects() {
            citySelect.innerHTML = '<option value="">-- Select City --</option>';
            citySelect.disabled = true;
            cityHidden.value = '';

            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;
            barangayHidden.value = '';
        }

        async function loadCities(provinceId, selectedCityId = null) {
            citySelect.disabled = true;
            citySelect.innerHTML = '<option>Loading cities…</option>';
            try {
                const res = await fetch(`/locations/cities/${provinceId}`, {
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
                if (selectedCityId) {
                    citySelect.value = selectedCityId;
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

        // --- Event listeners ---
        provinceSelect.addEventListener('change', async function() {
            resetSelects();
            provinceHidden.value = this.selectedOptions[0]?.dataset.id || '';
            if (!this.value) return;
            await loadCities(this.value);
        });

        citySelect.addEventListener('change', async function() {
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            barangaySelect.disabled = true;
            cityHidden.value = '';
            barangayHidden.value = '';
            cityHidden.value = this.selectedOptions[0]?.dataset.id || '';
            if (!this.value) return;
            await loadBarangays(this.value);
        });

        barangaySelect.addEventListener('change', function() {
            barangayHidden.value = this.selectedOptions[0]?.dataset.id || '';
        });

        modal.addEventListener('show.bs.modal', async function(event) {
            const button = event.relatedTarget;

            // Fill basic fields
            modal.querySelector('#edit_clinic_id').value = button.getAttribute('data-id') || '';
            modal.querySelector('#edit_clinic_name').value = button.getAttribute('data-name') || '';
            modal.querySelector('#edit_clinic_description').value = button.getAttribute(
                'data-description') || '';
            modal.querySelector('#edit_specialty').value = button.getAttribute('data-specialty') || '';
            modal.querySelector('#edit_email').value = button.getAttribute('data-email') || '';
            modal.querySelector('#edit_contact_no').value = button.getAttribute('data-contact_no') ||
            '';
            modal.querySelector('#edit_mobile_no').value = button.getAttribute('data-mobile_no') || '';
            modal.querySelector('#edit_house_no').value = button.getAttribute('data-house_no') || '';
            modal.querySelector('#edit_street').value = button.getAttribute('data-street') || '';
            modal.querySelector('#edit_schedule_summary').value = button.getAttribute(
                'data-schedule_summary') || '';

            const provinceId = button.getAttribute('data-province_id') || '';
            const cityId = button.getAttribute('data-city_id') || '';
            const barangayId = button.getAttribute('data-barangay_id') || '';

            // --- Prefill cascading selects ---
            if (provinceId) {
                provinceSelect.value = provinceId;
                provinceHidden.value = provinceSelect.selectedOptions[0]?.dataset.id || '';
                await loadCities(provinceId, cityId);
                if (cityId) {
                    cityHidden.value = citySelect.selectedOptions[0]?.dataset.id || '';
                    await loadBarangays(cityId, barangayId);
                    if (barangayId) {
                        barangayHidden.value = barangaySelect.selectedOptions[0]?.dataset.id || '';
                    }
                }
            }

            // Reset & populate schedule
            modal.querySelectorAll('.day-check').forEach(cb => {
                cb.checked = false;
                const row = cb.closest('.day-row');
                row.querySelectorAll('input[type="time"]').forEach(i => i.value = '');
                row.querySelector('.time-inputs').classList.add('d-none');
            });

            const schedules = JSON.parse(button.getAttribute('data-schedules') || '[]');
            schedules.forEach(sched => {
                const day = sched.day_of_week;
                const cb = modal.querySelector(`#edit_${day}`);
                if (cb) {
                    cb.checked = true;
                    const row = cb.closest('.day-row');
                    row.querySelector('.time-inputs').classList.remove('d-none');
                    row.querySelector(`input[name="schedule[${day}][start]"]`).value = sched
                        .start_time || '';
                    row.querySelector(`input[name="schedule[${day}][end]"]`).value = sched
                        .end_time || '';
                }
            });

            syncSchedule();
        });

    })();
</script>
<script>
    function syncSchedule() {
        document.querySelectorAll('#edit-clinic-modal .day-row').forEach(row => {
            const checkbox = row.querySelector('.day-check');
            const timeInputsWrapper = row.querySelector('.time-inputs');
            const timeInputs = timeInputsWrapper.querySelectorAll('input[type="time"]');

            if (checkbox.checked) {
                timeInputsWrapper.classList.remove('d-none');
                timeInputs.forEach(input => input.disabled = false);
            } else {
                timeInputsWrapper.classList.add('d-none');
                timeInputs.forEach(input => input.disabled = true);
            }

            // Add change event listener (if not already added)
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    timeInputsWrapper.classList.remove('d-none');
                    timeInputs.forEach(input => input.disabled = false);
                } else {
                    timeInputsWrapper.classList.add('d-none');
                    timeInputs.forEach(input => input.disabled = true);
                }
            });
        });
    }

    // Run it after the modal is shown
    document.getElementById('edit-clinic-modal').addEventListener('shown.bs.modal', function() {
        syncSchedule();
    });
</script>

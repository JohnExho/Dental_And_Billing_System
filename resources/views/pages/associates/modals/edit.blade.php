<!-- Edit associate Modal -->
<div class="modal fade" id="edit-associate-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="edit-associate-form" action="{{ route('process-update-associate') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="associate_id" id="edit_associate_id">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Edit Associate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- associate Name -->
                            <h6 class="text-muted mt-2">👤 associate Name</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="edit_first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="edit_first_name" name="first_name"
                                        required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="edit_last_name" name="last_name"
                                        required>
                                </div>
                            </div>

                            <!-- Contact -->
                            <h6 class="text-muted mt-3">☎️ Contact</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="edit_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_contact_no" class="form-label">Phone / Landline</label>
                                    <input type="text" class="form-control form-control-sm phone-number"
                                        id="edit_contact_no" name="contact_no" maxlength="11" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_mobile_no" class="form-label">Mobile No</label>
                                    <input type="text" class="form-control form-control-sm phone-number"
                                        id="edit_mobile_no" name="mobile_no" maxlength="11" required>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Address -->
                            <h6 class="text-muted mt-2">📍 Address</h6>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="edit_house_no" class="form-label">House No.</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_house_no"
                                        name="address[house_no]">
                                </div>
                                <div class="col-md-9 mb-3">
                                    <label for="edit_street" class="form-label">Street</label>
                                    <input type="text" class="form-control form-control-sm" id="edit_street"
                                        name="address[street]">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_province_select" class="form-label">Province</label>
                                    <select id="edit_province_select" class="form-select form-select-sm">
                                        <option value="">-- Select Province --</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->province_id }}" data-id="{{ $province->id }}">
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="address[province_id]" id="edit_province_hidden">
                                    <span id="province_label" class="form-text"></span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_city_select" class="form-label">City</label>
                                    <select id="edit_city_select" class="form-select form-select-sm" disabled
                                        >
                                        <option value="">-- Select City --</option>
                                    </select>
                                    <input type="hidden" name="address[city_id]" id="edit_city_hidden">
                                    <span id="city_label" class="form-text"></span>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="edit_barangay_select" class="form-label">Barangay</label>
                                    <select id="edit_barangay_select" class="form-select form-select-sm" disabled
                                        >
                                        <option value="">-- Select Barangay --</option>
                                    </select>
                                    <input type="hidden" name="address[barangay_id]" id="edit_barangay_hidden">
                                    <span id="barangay_label" class="form-text"></span>
                                </div>

                            </div>
                            <!-- Account Status -->
                            <h6 class="text-muted mt-3">⚡ Account Status</h6>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                    value="1">
                                <label class="form-check-label" for="edit_is_active">Activate/Deactivate</label>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">💾 Update associate</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">✖️ Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const modal = document.getElementById('edit-associate-modal');
        if (!modal) return;

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
                barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
                data.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.barangay_id;
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

        // --- Populate modal when opened ---
        modal.addEventListener('show.bs.modal', async function(event) {
            const button = event.relatedTarget;

            // Fill fields
            modal.querySelector('#edit_associate_id').value = button.getAttribute('data-id') || '';
            modal.querySelector('#edit_first_name').value = button.getAttribute('data-first_name') ||
                '';
            modal.querySelector('#edit_middle_name').value = button.getAttribute('data-middle_name') ||
                '';
            modal.querySelector('#edit_last_name').value = button.getAttribute('data-last_name') || '';
            modal.querySelector('#edit_email').value = button.getAttribute('data-email') || '';
            modal.querySelector('#edit_contact_no').value = button.getAttribute('data-contact_no') ||
                '';
            modal.querySelector('#edit_mobile_no').value = button.getAttribute('data-mobile_no') || '';
            modal.querySelector('#edit_house_no').value = button.getAttribute('data-house_no') || '';
            modal.querySelector('#edit_street').value = button.getAttribute('data-street') || '';
            modal.querySelector('#province_label').textContent = button.getAttribute(
                'data-province_name') || '';
            modal.querySelector('#city_label').textContent = button.getAttribute('data-city_name') ||
                '';
            modal.querySelector('#barangay_label').textContent = button.getAttribute(
                'data-barangay_name') || '';
            modal.querySelector('#edit_is_active').checked = button.getAttribute('data-is_active') ===
                '1';

            if (provinceId) {
                provinceSelect.value = provinceId;
                provinceHidden.value = provinceSelect.selectedOptions[0]?.dataset.id || '';
                await loadCities(provinceId, cityId);
                if (cityId) await loadBarangays(cityId, barangayId);
            }
        });

        // --- Phone formatting ---
        modal.querySelectorAll('.phone-number').forEach(input => {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                this.value = value;
            });
        });
    })();
</script>

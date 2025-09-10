<!-- Edit Laboratory Modal -->
<div class="modal fade" id="edit-laboratory-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <form id="edit-laboratory-form" action="{{ route('process-update-laboratory') }}"  method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="laboratory_id" id="edit_laboratory_id">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Edit Laboratory</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-12">
                            <!-- Basic Info -->
                            <div class="card border-0 shadow-sm p-3 mb-4">
                                <h6 class="fw-bold mb-3">🧪 Basic Information</h6>
                                <div class="mb-3">
                                    <label for="edit_laboratory_name" class="form-label">Laboratory Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_laboratory_name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_laboratory_description" class="form-label">Short Description</label>
                                    <input class="form-control" id="edit_laboratory_description" name="description">
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="card border-0 shadow-sm p-3 mb-4">
                                <h6 class="fw-bold mb-3">☎️ Contact Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="edit_speciality" class="form-label">Speciality</label>
                                        <input type="text" class="form-control" id="edit_speciality" name="speciality">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="edit_email" name="email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_contact_no" class="form-label">Phone / Landline</label>
                                        <input type="text" class="form-control phone-number" id="edit_contact_no" name="contact_no" maxlength="11">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_mobile_no" class="form-label">Mobile No</label>
                                        <input type="text" class="form-control phone-number" id="edit_mobile_no" name="mobile_no" maxlength="11">
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="card border-0 shadow-sm p-3">
                                <h6 class="fw-bold mb-3">📍 Address</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="edit_house_no" class="form-label">House No.</label>
                                        <input type="text" class="form-control" id="edit_house_no" name="address[house_no]">
                                    </div>
                                    <div class="col-md-5">
                                        <label for="edit_street" class="form-label">Street</label>
                                        <input type="text" class="form-control" id="edit_street" name="address[street]">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_province_select" class="form-label">Province <span class="text-danger">*</span></label>
                                        <select id="edit_province_select" class="form-select" required>
                                            <option value="">-- Select Province --</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->province_id }}" data-id="{{ $province->id }}">
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="address[province_id]" id="edit_province_hidden">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_city_select" class="form-label">City <span class="text-danger">*</span></label>
                                        <select id="edit_city_select" class="form-select" disabled required>
                                            <option value="">-- Select City --</option>
                                        </select>
                                        <input type="hidden" name="address[city_id]" id="edit_city_hidden">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_barangay_select" class="form-label">Barangay <span class="text-danger">*</span></label>
                                        <select id="edit_barangay_select" class="form-select" disabled required>
                                            <option value="">-- Select Barangay --</option>
                                        </select>
                                        <input type="hidden" name="address[barangay_id]" id="edit_barangay_hidden">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success px-4">💾 Update Laboratory</button>
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">✖ Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const modal = document.getElementById('edit-laboratory-modal');
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

            // Fill basic fields
            modal.querySelector('#edit_laboratory_id').value = button.getAttribute('data-id') || '';
            modal.querySelector('#edit_laboratory_name').value = button.getAttribute('data-name') || '';
            modal.querySelector('#edit_laboratory_description').value = button.getAttribute(
                'data-description') || '';
            modal.querySelector('#edit_speciality').value = button.getAttribute('data-speciality') || '';
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
        });
    })();
</script>

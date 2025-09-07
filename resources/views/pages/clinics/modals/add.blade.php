<!-- Add Clinic Modal -->
<div class="modal fade" id="add-clinic-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <form action="{{ route('process-create-clinic') }}" method="POST">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">
            <i class="bi bi-hospital me-2"></i> Add New Clinic
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-6">
              <!-- Clinic Info -->
              <h6 class="text-muted mb-2"><i class="bi bi-building me-1"></i> Clinic Information</h6>
              <div class="mb-3">
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-building"></i></span>
                  <input type="text" class="form-control" id="clinic_name" name="name"
                         placeholder="Clinic Name"
                         value="{{ old('name', $clinic->name ?? '') }}" required>
                </div>
              </div>

              <div class="mb-3">
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-info-circle"></i></span>
                  <input type="text" class="form-control" id="clinic_description" name="description"
                         placeholder="Short Description"
                         value="{{ old('description', $clinic->clinic_description ?? '') }}">
                </div>
              </div>

              <!-- Contact -->
              <h6 class="text-muted mt-4 mb-2"><i class="bi bi-envelope me-1"></i> Contact</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-heart-pulse"></i></span>
                    <input type="text" class="form-control" id="specialty" name="specialty"
                           placeholder="Specialty"
                           value="{{ old('specialty', $clinic->specialty ?? '') }}">
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="Email address"
                           value="{{ old('email', $clinic->email ?? '') }}"
                           pattern="^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$"
                           required>
                  </div>
                  <div class="form-text">Must start with a letter (e.g. clinic@mail.com)</div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="text" class="form-control phone-number" id="contact_no" name="contact_no"
                           placeholder="Phone / Landline"
                           value="{{ old('contact_no', $clinic->contact_no ?? '') }}" maxlength="11" required>
                  </div>
                  <div class="form-text">11-digit format only</div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    <input type="text" class="form-control phone-number" id="mobile_no" name="mobile_no"
                           placeholder="Mobile No"
                           value="{{ old('mobile_no', $clinic->mobile_no ?? '') }}" maxlength="11" required>
                  </div>
                  <div class="form-text">11-digit format only</div>
                </div>
              </div>

              <!-- Address -->
              <h6 class="text-muted mt-4 mb-2"><i class="bi bi-geo-alt me-1"></i> Address</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <input type="text" class="form-control" id="house_no" name="address[house_no]"
                         placeholder="House No."
                         value="{{ old('house_no', $clinic->house_no ?? '') }}">
                </div>
                <div class="col-md-8 mb-3">
                  <input type="text" class="form-control" id="street" name="address[street]"
                         placeholder="Street"
                         value="{{ old('street', $clinic->street ?? '') }}">
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 mb-3">
                  <select id="province-select" class="form-select" required>
                    <option value="" title="">-- Province --</option>
                    @foreach($provinces as $province)
                      <option value="{{ $province->province_id }}" data-id="{{ $province->id }}"
                        {{ old('province_id', $clinic->province_id ?? '') == $province->id ? 'selected' : '' }}>
                        {{ $province->name }}
                      </option>
                    @endforeach
                  </select>
                  <input type="hidden" name="address[province_id]" id="province-hidden">
                </div>
                <div class="col-md-12 mb-3">
                  <select id="city-select" class="form-select" disabled required>
                    <option value="">-- City --</option>
                  </select>
                  <input type="hidden" name="address[city_id]" id="city-hidden">
                </div>
                <div class="col-md-12 mb-3">
                  <select id="barangay-select" class="form-select" disabled required>
                    <option value="">-- Barangay --</option>
                  </select>
                  <input type="hidden" name="address[barangay_id]" id="barangay-hidden">
                </div>
              </div>
            </div>

            <!-- Right Column: Schedule -->
            <div class="col-md-6">
              <h6 class="text-muted mb-2"><i class="bi bi-calendar-week me-1"></i> Schedule</h6>
              <div class="mb-3">
                <input type="text" class="form-control" id="schedule_summary" name="schedule_summary"
                       placeholder="Summary (e.g. Mon-Fri 9AM-5PM)"
                       value="{{ old('schedule_summary', $clinic->schedule_summary ?? '') }}" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Weekly Schedule</label>
                <div class="row">
                  @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                    <div class="day-row col-12 mb-2">
                      <div class="form-check mb-1">
                        <input class="form-check-input day-check" type="checkbox" id="{{ strtolower($day) }}"
                               name="schedule[{{ $day }}][active]" {{ old('schedule.' . $day . '.active') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="{{ strtolower($day) }}">{{ $day }}</label>
                      </div>
                      <div class="d-flex time-inputs d-none">
                        <input type="time" class="form-control me-2" name="schedule[{{ $day }}][start]"
                               value="{{ old('schedule.' . $day . '.start') }}">
                        <input type="time" class="form-control" name="schedule[{{ $day }}][end]"
                               value="{{ old('schedule.' . $day . '.end') }}">
                      </div>
                    </div>
                  @endforeach
                </div>
                <div class="form-text">Select days and set opening/closing hours.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-save me-1"></i> Save Clinic
          </button>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
(function () {
  const modal = document.getElementById('add-clinic-modal');
  if (!modal) return;

const form = modal.querySelector('form');

// --- Weekly schedule toggle ---
function syncSchedule() {
  modal.querySelectorAll('.day-row').forEach(row => {
    const cb = row.querySelector('.day-check');
    const times = row.querySelector('.time-inputs');
    const inputs = times.querySelectorAll('input[type="time"]');
    if (!cb || !times) return;

    if (cb.checked) {
      times.classList.remove('d-none');
      inputs.forEach(i => i.required = true); // ✅ required when active
    } else {
      times.classList.add('d-none');
      inputs.forEach(i => {
        i.required = false; // ✅ remove required
        i.value = '';
      });
    }
  });
}

modal.addEventListener('change', function(e) {
  if (e.target.classList.contains('day-check')) {
    const row = e.target.closest('.day-row');
    const times = row.querySelector('.time-inputs');
    const inputs = times.querySelectorAll('input[type="time"]');

    if (e.target.checked) {
      times.classList.remove('d-none');
      inputs.forEach(i => i.required = true); // ✅ required
    } else {
      times.classList.add('d-none');
      inputs.forEach(i => {
        i.required = false; // ✅ not required
        i.value = '';
      });
    }
  }
});


  // --- Address cascading ---
  const provinceSelect = modal.querySelector('#province-select');
  const provinceHidden = modal.querySelector('#province-hidden');
  const citySelect = modal.querySelector('#city-select');
  const cityHidden = modal.querySelector('#city-hidden');
  const barangaySelect = modal.querySelector('#barangay-select');
  const barangayHidden = modal.querySelector('#barangay-hidden');

  function resetSelects() {
    citySelect.innerHTML = '<option value="">-- Select City --</option>';
    citySelect.disabled = true;
    cityHidden.value = '';
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    barangaySelect.disabled = true;
    barangayHidden.value = '';
  }

  let listenersAttached = false;
  function attachListeners() {
    if (listenersAttached) return;
    listenersAttached = true;

    // Province -> Cities
    provinceSelect.addEventListener('change', async function() {
      resetSelects();
      const selected = this.selectedOptions[0];
      provinceHidden.value = selected.dataset.id || '';
      const provinceCode = this.value;
      if (!provinceCode) return;

      citySelect.innerHTML = '<option>Loading cities…</option>';
      try {
        const res = await fetch(`/locations/cities/${provinceCode}`, {headers:{'Accept':'application/json'}});
        const data = await res.json();
        citySelect.innerHTML = '<option value="">-- Select City --</option>';
        data.forEach(c => {
          const opt = document.createElement('option');
          opt.value = c.city_id;
          opt.title = c.name;
          opt.dataset.id = c.id;
          opt.textContent = c.name;
          citySelect.appendChild(opt);
        });
        citySelect.disabled = false;
      } catch(err) {
        console.error(err);
        citySelect.innerHTML = '<option value="">Error loading cities</option>';
      }
    });

    // City -> Barangays
    citySelect.addEventListener('change', async function() {
      barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
      barangaySelect.disabled = true;
      cityHidden.value = '';
      barangayHidden.value = '';

      const selectedCity = this.selectedOptions[0];
      cityHidden.value = selectedCity.dataset.id || '';
      const cityCode = this.value;
      if (!cityCode) return;

      // fetch barangays
      barangaySelect.innerHTML = '<option>Loading barangays…</option>';
      try {
        const res = await fetch(`/locations/barangays/${cityCode}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
        data.forEach(b => {
          const opt = document.createElement('option');
          opt.value = b.barangay_id;
          opt.dataset.id = b.id; // database ID
          opt.textContent = b.name;
          barangaySelect.appendChild(opt);
        });
        barangaySelect.disabled = false;

        // Update hidden input when user selects a barangay
        barangaySelect.addEventListener('change', function() {
          const selectedBarangay = this.selectedOptions[0];
          barangayHidden.value = selectedBarangay ? selectedBarangay.dataset.id : '';
        });

      } catch(err) {
        console.error(err);
        barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
      }
    });
  }

  modal.addEventListener('shown.bs.modal', function() {
    resetSelects();
    attachListeners();
    provinceSelect.focus();
    syncSchedule();
  });

  modal.addEventListener('hidden.bs.modal', function() {
    provinceSelect.value = '';
    resetSelects();
  });

  // --- Validation: Require at least one day checked ---
  form.addEventListener('submit', function(e) {
    const checkboxes = form.querySelectorAll('.day-check');
    const atLeastOneChecked = Array.from(checkboxes).some(cb => cb.checked);
    if (!atLeastOneChecked) {
      e.preventDefault();
                  Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            position: 'top-right',
            timer: 3000,
            text: 'Please select at least one day in the weekly schedule.',
            toast: true,
            showConfirmButton: false,
            timerProgressBar: true
        });
    }
  });

    const phoneInputs = modal.querySelectorAll('.phone-number');

phoneInputs.forEach(input => {
    input.addEventListener('input', function(e) {
        // Remove non-digit characters
        let value = this.value.replace(/\D/g, '');

        // Limit to 11 digits
        if (value.length > 11) value = value.slice(0, 11);

        this.value = value;
    }); 
});
form.addEventListener('submit', function(e) {
    const emailInput = form.querySelector('#email');
    const phoneInputs = form.querySelectorAll('.phone-number');

    // --- Check phone numbers ---
    let phoneValid = true;
    phoneInputs.forEach(input => {
        if (input.value.length !== 11) phoneValid = false;
    });

    // --- Check email ---
    const emailPattern = /^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
    const emailValid = emailPattern.test(emailInput.value);

    if (!phoneValid) {
        e.preventDefault();
          Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            position: 'top-right',
            timer: 3000,
            text: 'Phone numbers must be exactly 11 digits.',
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
            title: 'Oops!',
            position: 'top-right',
            timer: 3000,
            text: 'Email must start with a letter and be valid.',
            toast: true,
            showConfirmButton: false,
            timerProgressBar: true
        });
        return;
    }
});

})();
</script>

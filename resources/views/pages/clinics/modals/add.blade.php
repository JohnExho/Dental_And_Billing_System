<!-- Add Clinic Modal -->
<div class="modal fade" id="add-clinic-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form action="{{ route('process-create-clinic') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add Clinic</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
              <!-- Clinic fields unchanged -->
              <div class="mb-3">
                <label for="clinic_name" class="form-label">Clinic Name</label>
                <input type="text" class="form-control" id="clinic_name" name="name"
                       value="{{ old('name', $clinic->name ?? '') }}" required>
              </div>

              <div class="mb-3">
                <label for="clinic_description" class="form-label">Short Description</label>
                <input type="text" class="form-control" id="clinic_description" name="description"
                       value="{{ old('description', $clinic->clinic_description ?? '') }}">
              </div>

              <!-- Contact -->
              <div class="row">
                <h6 class="text-muted mt-3">☎️ Contact</h6>
                <div class="col-md-6 mb-3">
                  <label for="specialty" class="form-label">Specialty</label>
                  <input type="text" class="form-control form-control-sm" id="specialty" name="specialty"
                         value="{{ old('specialty', $clinic->specialty ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email"
                         value="{{ old('email', $clinic->email ?? '') }}"
                          pattern="^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$"
       title="Email must start with a letter and be valid." required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="contact_no" class="form-label">Phone / Landline</label>
                  <input type="text" class="form-control form-control-sm phone-number" id="contact_no" name="contact_no"
                         value="{{ old('contact_no', $clinic->contact_no ?? '') }}"  maxlength="11" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="mobile_no" class="form-label">Mobile No</label>
                  <input type="text" class="form-control phone-number" id="mobile_no" name="mobile_no"
                         value="{{ old('mobile_no', $clinic->mobile_no ?? '') }}"  maxlength="11" required>
                </div>
              </div>

              <!-- Address -->
              <h6 class="text-muted mt-3">📍 Address</h6>
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label for="house_no" class="form-label">House No.</label>
                  <input type="text" class="form-control form-control-sm" id="house_no" name="address[house_no]"
                         value="{{ old('house_no', $clinic->house_no ?? '') }}">
                </div>

                <div class="col-md-5 mb-3">
                  <label for="street" class="form-label">Street</label>
                  <input type="text" class="form-control form-control-sm" id="street" name="address[street]"
                         value="{{ old('street', $clinic->street ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                  <label for="province-select" class="form-label">Province</label>
                  <select id="province-select" class="form-select" required>
                    <option value="">-- Select Province --</option>
                    @foreach($provinces as $province)
                      <option value="{{ $province->province_id }}" data-id="{{ $province->id }}"
                        {{ old('province_id', $clinic->province_id ?? '') == $province->id ? 'selected' : '' }}>
                        {{ $province->name }}
                      </option>
                    @endforeach
                  </select>
                  <input type="hidden" name="address[province_id]" id="province-hidden">
                </div>

                <div class="col-md-4 mb-3">
                  <label for="city-select" class="form-label">City</label>
                  <select id="city-select" class="form-select" disabled required>
                    <option value="">-- Select City --</option>
                  </select>
                  <input type="hidden" name="address[city_id]" id="city-hidden">
                </div>

                <div class="col-md-4 mb-3">
                  <label for="barangay-select" class="form-label">Barangay</label>
                  <select id="barangay-select" class="form-select" disabled required>
                    <option value="">-- Select Barangay --</option>
                  </select>
                  <input type="hidden" name="address[barangay_id]" id="barangay-hidden">
                </div>
              </div>
            </div>

            <!-- Right Column: Schedule -->
            <div class="col-md-6">
              <div class="mb-3">
                <label for="schedule_summary" class="form-label">Schedule Summary</label>
                <input type="text" class="form-control" id="schedule_summary" name="schedule_summary"
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
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Clinic</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
      if (!cb || !times) return;
      times.classList.toggle('d-none', !cb.checked);
    });
  }

  modal.addEventListener('change', function(e) {
    if (e.target.classList.contains('day-check')) {
      const row = e.target.closest('.day-row');
      const times = row.querySelector('.time-inputs');
      times.classList.toggle('d-none', !e.target.checked);
      if (!e.target.checked) times.querySelectorAll('input[type="time"]').forEach(i => i.value='');
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

<!-- Add Waitlist Modal -->
<div class="modal fade" id="add-waitlist-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <form action="{{ route('process-create-waitlist') }}" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i> Add New Waitlist
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0" style="min-height: 60vh;">
                        <!-- Left: Patient List -->
                        <div class="col-md-5 border-end p-3" style="max-height: 60vh; overflow: hidden;">
                            <h6 class="fw-semibold mb-2"><i class="bi bi-person me-1"></i> Patients</h6>
                            
                            <!-- Search Input -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" id="patient_search" class="form-control" placeholder="Search patients...">
                                </div>
                            </div>

                            <!-- Patient List Container -->
                            <div id="patient_list_container" style="max-height: calc(60vh - 100px); overflow-y: auto;">
                                <ul class="list-group" id="waitlist_patient_list">
                                    @foreach ($patients as $patient)
                                        <li class="list-group-item list-group-item-action" data-id="{{ $patient->patient_id }}">
                                            {{ $patient->full_name }}
                                        </li>
                                    @endforeach
                                </ul>
                                <div id="patient_loading" class="text-center py-3 d-none">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <div id="patient_no_results" class="text-center text-muted py-3 d-none">
                                    <i class="bi bi-search"></i>
                                    <p class="mb-0">No patients found</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Waitlist Form -->
                        <div class="col-md-7 p-3">
                            <input type="hidden" name="patient_id" id="waitlist_patient_id">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><i class="bi bi-building me-1"></i> Clinic</h6>
                                <input type="text" class="form-control" 
                                    value="{{ \App\Models\Clinic::find(session('clinic_id'))->name }}" disabled>
                                <input type="hidden" name="clinic_id" value="{{ session('clinic_id') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-person-workspace me-1"></i> Associate</label>
                                <select id="provider-associate" name="associate_id" class="form-select">
                                    <option value="">-- Select Associate --</option>
                                    @forelse ($associates as $associate)
                                        <option value="{{ $associate->associate_id }}">
                                            {{ trim($associate->full_name) !== '' ? $associate->full_name : 'N/A' }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No Available Associate</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar-check me-1"></i>Status</label>
                                <select name="status" id="queue-status" class="form-select" disabled>
                                    <option value="waiting">Waiting</option>
                                    <option value="in_consultation">In Consultation</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Waitlist
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('pages.patients.modals.add')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('add-waitlist-modal');
    if (!modal) return;

    const form = modal.querySelector('form');
    if (!form) return;

    const patientListContainer = document.getElementById('waitlist_patient_list');
    const hiddenInput = document.getElementById('waitlist_patient_id');
    const searchInput = document.getElementById('patient_search');
    const loadingIndicator = document.getElementById('patient_loading');
    const noResultsIndicator = document.getElementById('patient_no_results');

    let searchTimeout;

    // --- Patient list selection ---
    function attachPatientClickEvents() {
        const patientItems = modal.querySelectorAll('#waitlist_patient_list .list-group-item');
        
        patientItems.forEach(item => {
            item.addEventListener('click', () => {
                // Highlight selected patient
                patientItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');

                // Store selected patient id
                hiddenInput.value = item.dataset.id;
            });
        });
    }

    // Initial attachment
    attachPatientClickEvents();

    // --- Search functionality ---
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();

            searchTimeout = setTimeout(() => {
                if (searchTerm.length === 0) {
                    // Reset to original list if search is cleared
                    location.reload();
                    return;
                }

                // Show loading
                loadingIndicator.classList.remove('d-none');
                noResultsIndicator.classList.add('d-none');
                patientListContainer.innerHTML = '';

                // Fetch patients
                fetch(`{{ route('patients.all') }}?search=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingIndicator.classList.add('d-none');
                        
                        if (data.length === 0) {
                            noResultsIndicator.classList.remove('d-none');
                            return;
                        }

                        // Populate list
                        patientListContainer.innerHTML = data.map(patient => `
                            <li class="list-group-item list-group-item-action" data-id="${patient.patient_id}">
                                ${patient.full_name}
                            </li>
                        `).join('');

                        // Re-attach click events
                        attachPatientClickEvents();
                    })
                    .catch(error => {
                        loadingIndicator.classList.add('d-none');
                        console.error('Error fetching patients:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to search patients. Please try again.',
                            toast: true,
                            position: 'top-end',
                            timer: 3000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });
                    });
            }, 300); // Debounce 300ms
        });
    }

    // --- Add patient via select (if exists) ---
    const patientSelect = document.getElementById('provider-patient');
    if (patientSelect) {
        patientSelect.addEventListener('change', function() {
            if (this.value === 'add_new') {
                const addPatientModal = new bootstrap.Modal(document.getElementById('add-patient-modal'));
                const waitlistModal = bootstrap.Modal.getInstance(modal);
                if (waitlistModal) waitlistModal.hide();
                addPatientModal.show();
                this.value = "";
            }
        });
    }

    // --- Phone & Email validation ---
    const phoneInputs = modal.querySelectorAll('.phone-number');

    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            this.value = value;
        });
    });

    form.addEventListener('submit', function(e) {
        const emailInput = form.querySelector('input[name="email"]');

        let phoneValid = true;
        phoneInputs.forEach(input => {
            if (input.value.length !== 11) phoneValid = false;
        });

        const emailPattern = /^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
        const emailValid = emailInput ? emailPattern.test(emailInput.value) : true;

        if (!phoneValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                position: 'top-end',
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
                position: 'top-end',
                timer: 3000,
                text: 'Email must start with a letter and be valid.',
                toast: true,
                showConfirmButton: false,
                timerProgressBar: true
            });
        }
    });
});
</script>
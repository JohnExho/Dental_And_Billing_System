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
                        <div class="col-md-5 border-end" style="max-height: 60vh; overflow: hidden;">
                            <div class="p-3">
                                <h6 class="fw-semibold mb-2"><i class="bi bi-person me-1"></i> Patients</h6>
                                
                                <!-- Search Input -->
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" 
                                           id="patient_search" 
                                           class="form-control" 
                                           placeholder="Search by name (e.g., Doe, John)">
                                    <button class="btn btn-outline-secondary" type="button" id="clear-patient-search">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                
                                <small class="text-muted d-block mb-2">
                                    <span id="patient-search-status">
                                        Total: <span id="patient-result-count">{{ $patients->count() }}</span> patient(s)
                                    </span>
                                </small>
                            </div>

                            <!-- Patient List Container -->
                            <div id="patient_list_container" style="max-height: calc(60vh - 130px); overflow-y: auto;" class="px-3">
                                <ul class="list-group" id="waitlist_patient_list">
                                    @foreach ($patients as $patient)
                                        <li class="list-group-item list-group-item-action patient-item" 
                                            data-id="{{ $patient->patient_id }}"
                                            data-search-name="{{ strtolower($patient->full_name) }}">
                                            {{ $patient->full_name }}
                                        </li>
                                    @endforeach
                                </ul>
                                
                                <!-- Loading Spinner -->
                                <div id="patient_loading" class="text-center py-3 d-none">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted small">Searching all patients...</p>
                                </div>
                                
                                <!-- No Results Message -->
                                <div id="patient_no_results" class="text-center text-muted py-3 d-none">
                                    <i class="bi bi-search fs-3 d-block mb-2"></i>
                                    <p class="mb-0">No patients found matching your search.</p>
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Waitlist
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
    const clearBtn = document.getElementById('clear-patient-search');
    const loadingIndicator = document.getElementById('patient_loading');
    const noResultsIndicator = document.getElementById('patient_no_results');
    const resultCount = document.getElementById('patient-result-count');
    const searchStatus = document.getElementById('patient-search-status');

    let allPatients = [];
    let searchTimeout = null;
    let isSearching = false;
    let preselectedPatientId = null;

    // Store initial patient items
    const initialPatientItems = Array.from(document.querySelectorAll('.patient-item'));

    // --- Handle preselection when modal opens from patient table ---
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        
        if (button && button.dataset.patientId) {
            preselectedPatientId = button.dataset.patientId;
            
            // Auto-select the patient in the list after modal is shown
            setTimeout(() => {
                selectPatientById(preselectedPatientId);
            }, 100);
        }
    });

    // --- Function to select patient by ID ---
    function selectPatientById(patientId) {
        const patientItems = modal.querySelectorAll('.patient-item');
        
        patientItems.forEach(item => {
            if (item.dataset.id === patientId) {
                // Highlight selected patient
                patientItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                
                // Store selected patient id
                hiddenInput.value = patientId;
                
                // Scroll into view
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    // --- Reset when modal closes ---
    modal.addEventListener('hidden.bs.modal', function() {
        preselectedPatientId = null;
        
        // Clear selection
        const patientItems = modal.querySelectorAll('.patient-item');
        patientItems.forEach(i => i.classList.remove('active'));
        hiddenInput.value = '';
        
        // Clear search if active
        if (searchInput.value !== '') {
            searchInput.value = '';
            restoreInitialView();
        }
    });

    // --- Patient list selection ---
    function attachPatientClickEvents() {
        const patientItems = modal.querySelectorAll('.patient-item');
        
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

    // --- Debounced search function ---
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // If empty, restore initial view
        if (searchTerm === '') {
            restoreInitialView();
            return;
        }

        // Debounce: wait 300ms after user stops typing
        searchTimeout = setTimeout(() => {
            fetchAndSearch(searchTerm);
        }, 300);
    }

    // --- Fetch all patients and search ---
    async function fetchAndSearch(searchTerm) {
        try {
            isSearching = true;
            showLoading();

            // Fetch all patients
            const response = await fetch('{{ route("patients.all") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch patients');

            allPatients = await response.json();
            
            // Filter patients based on search term
            const filtered = allPatients.filter(patient => {
                const name = (patient.full_name || '').toLowerCase();
                return name.includes(searchTerm);
            });

            displaySearchResults(filtered);
            
        } catch (error) {
            console.error('Search error:', error);
            searchCurrentPage(searchTerm);
        }
    }

    // --- Display search results ---
    function displaySearchResults(patientsData) {
        hideLoading();

        if (patientsData.length === 0) {
            patientListContainer.style.display = 'none';
            noResultsIndicator.classList.remove('d-none');
            searchStatus.innerHTML = 'Found: <span class="text-danger">0</span> patient(s)';
            return;
        }

        // Clear and populate list
        patientListContainer.innerHTML = '';
        noResultsIndicator.classList.add('d-none');
        patientListContainer.style.display = '';

        patientsData.forEach(patient => {
            const li = createPatientListItem(patient);
            patientListContainer.appendChild(li);
        });

        searchStatus.innerHTML = `Found: <span class="text-success">${patientsData.length}</span> patient(s)`;

        // Re-attach click events
        attachPatientClickEvents();
        
        // Re-select patient if there was a preselection
        if (preselectedPatientId) {
            selectPatientById(preselectedPatientId);
        }
    }

    // --- Create patient list item ---
    function createPatientListItem(patient) {
        const li = document.createElement('li');
        li.className = 'list-group-item list-group-item-action patient-item';
        li.dataset.id = patient.patient_id;
        li.dataset.searchName = (patient.full_name || '').toLowerCase();
        li.textContent = patient.full_name;
        return li;
    }

    // --- Fallback: Search only current page ---
    function searchCurrentPage(searchTerm) {
        hideLoading();
        let visibleCount = 0;

        initialPatientItems.forEach(item => {
            const name = item.dataset.searchName || '';
            const matches = name.includes(searchTerm);

            if (matches) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            noResultsIndicator.classList.remove('d-none');
            patientListContainer.style.display = 'none';
        } else {
            noResultsIndicator.classList.add('d-none');
            patientListContainer.style.display = '';
        }

        searchStatus.innerHTML = `Found: ${visibleCount} patient(s) <span class="text-warning">(current page only)</span>`;
    }

    // --- Restore initial view ---
    function restoreInitialView() {
        isSearching = false;
        hideLoading();
        noResultsIndicator.classList.add('d-none');
        patientListContainer.style.display = '';
        searchStatus.innerHTML = 'Total: <span id="patient-result-count">{{ $patients->count() }}</span> patient(s)';

        // Restore original items
        patientListContainer.innerHTML = '';
        initialPatientItems.forEach(item => {
            item.style.display = '';
            patientListContainer.appendChild(item.cloneNode(true));
        });

        attachPatientClickEvents();
        
        // Re-select patient if there was a preselection
        if (preselectedPatientId) {
            selectPatientById(preselectedPatientId);
        }
    }

    // --- Show/Hide loading ---
    function showLoading() {
        patientListContainer.style.display = 'none';
        noResultsIndicator.classList.add('d-none');
        loadingIndicator.classList.remove('d-none');
    }

    function hideLoading() {
        loadingIndicator.classList.add('d-none');
    }

    // --- Event listeners ---
    searchInput.addEventListener('input', performSearch);

    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        restoreInitialView();
        searchInput.focus();
    });

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
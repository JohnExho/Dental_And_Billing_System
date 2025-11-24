<div class="card-body p-0">
    <!-- Search Input -->
    <div class="p-3 bg-light border-bottom">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="patient-search" class="form-control"
                placeholder="Search by patient name (e.g., Bins, Ignatius)">
            <button class="btn btn-outline-secondary" type="button" id="clear-search">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <small class="text-muted d-block mt-2">
            <span id="search-status">
                Total: <span id="result-count">{{ $patients->total() }}</span> patient(s)
            </span>
        </small>
    </div>

    @if ($patients->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No patient found. Add one using the
            button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-striped table-primary" id="patients-table">
                <thead class="bg-info">
                    <tr>
                        <th>Profile Pic</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="patients-tbody">
                    @foreach ($patients as $patient)
                        @php
                            $defaultProfile = match ($patient->sex) {
                                'male' => asset('public/images/defaults/male.png'),
                                'female' => asset('public/images/defaults/female.png'),
                                default => asset('public/images/defaults/other.png'),
                            };
$profileUrl = $patient->profile_picture
    ? asset('public/storage/' . $patient->profile_picture)
    : $defaultProfile;

                        @endphp

                        <tr class="patient-row" data-patient-id="{{ $patient->patient_id }}"
                            data-search-name="{{ strtolower($patient->full_name) }}"
                            data-search-contact="{{ strtolower($patient->mobile_no ?? '') }} {{ strtolower($patient->contact_no ?? '') }}"
                            data-search-email="{{ strtolower($patient->email ?? '') }}"
                            data-search-address="{{ strtolower($patient->full_address) }}">
                            <td>
<img src="{{ asset( $profileUrl) }}"
     alt="{{ $patient->full_name ?? 'Profile' }}"
     class="rounded-circle object-fit-cover border-primary border border-2"
     style="width: 60px; height: 60px;">

                            </td>
                            <td>{{ $patient->full_name }}</td>
                            <td>{{ $patient->mobile_no ?? 'N/A' }}/<br>{{ $patient->contact_no ?? 'N/A' }}</td>
                            <td>{{ $patient->email ?? 'N/A' }}</td>
                            <td>{{ $patient->full_address }}</td>

                            <td class="text-end">
<button type="button" 
        class="btn btn-outline-dark btn-sm" 
        data-bs-toggle="modal"
        data-bs-target="#add-waitlist-modal"
        data-patient-id="{{ $patient->patient_id }}">
                                    <i class="fa-solid fa-hourglass-start"></i>
                            </button>

                                <form action="{{ route('specific-patient') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </form>

                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-patient-modal" data-id="{{ $patient->patient_id }}"
                                    data-name="{{ $patient->full_name }}"
                                    data-contact="{{ $patient->mobile_no }} | {{ $patient->contact_no }}"
                                    data-email="{{ $patient->email }}"
                                    data-date_of_birth="{{ $patient->date_of_birth }}" data-sex="{{ $patient->sex }}"
                                    data-civil_status="{{ $patient->civil_status }}"
                                    data-occupation="{{ $patient->occupation }}"
                                    data-company="{{ $patient->company }}" data-referral="{{ $patient->referral }}"
                                    data-house_no="{{ optional($patient->address)->house_no }}"
                                    data-street="{{ optional($patient->address)->street }}"
                                    data-province_id="{{ optional($patient->address->province)->province_id }}"
                                    data-province_name="{{ optional($patient->address->province)->name }}"
                                    data-city_id="{{ optional($patient->address->city)->city_id }}"
                                    data-city_name="{{ optional($patient->address->city)->name }}"
                        data-barangay_id="{{ optional($patient->address)->barangay_id }}"
                        data-barangay_name="{{ optional($patient->address->barangay)->name }}"
                                    data-profile_picture="{{ $patient->profile_picture ? asset('storage/' . $patient->profile_picture) : '' }}"
                                    data-weight="{{ $patient->weight }}" data-height="{{ $patient->height }}"
                                    data-school="{{ $patient->school }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button type="button" class="btn btn-outline-danger btn-sm delete-patient-btn"
                                    data-id="{{ $patient->patient_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- No Results Message -->
            <div id="no-results" class="p-4 text-center text-muted d-none">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                <p class="mb-0">No patients found matching your search.</p>
            </div>

            <!-- Loading Spinner -->
            <div id="search-loading" class="p-4 text-center d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 text-muted">Searching all patients...</p>
            </div>
        </div>

        <!-- Pagination (hidden during search) -->
        <div id="pagination-container" class="p-3">
            {{ $patients->links() }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('patient-search');
        const clearBtn = document.getElementById('clear-search');
        const resultCount = document.getElementById('result-count');
        const searchStatus = document.getElementById('search-status');
        const tableBody = document.getElementById('patients-tbody');
        const noResults = document.getElementById('no-results');
        const searchLoading = document.getElementById('search-loading');
        const paginationContainer = document.getElementById('pagination-container');

        let allPatients = []; // Store all patients when searching
        let searchTimeout = null;
        let isSearching = false;

        // Initial patient rows from current page
        const initialRows = Array.from(document.querySelectorAll('.patient-row'));

        // Debounced search function
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();

            // Clear previous timeout
            clearTimeout(searchTimeout);

            // If empty, restore pagination view
            if (searchTerm === '') {
                restorePaginationView();
                return;
            }

            // Debounce: wait 300ms after user stops typing
            searchTimeout = setTimeout(() => {
                fetchAndSearch(searchTerm);
            }, 300);
        }

        // Fetch all patients and search
        async function fetchAndSearch(searchTerm) {
            try {
                isSearching = true;
                showLoading();

                // Fetch all patients (you need to create this route)
                const response = await fetch('{{ route('patients.all') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch patients');

                allPatients = await response.json();

                // Filter patients based on search term (name only)
                const filtered = allPatients.filter(patient => {
                    const name = (patient.full_name || '').toLowerCase();
                    return name.includes(searchTerm);
                });

                displaySearchResults(filtered);

            } catch (error) {
                console.error('Search error:', error);
                // Fallback to current page search if AJAX fails
                searchCurrentPage(searchTerm);
            }
        }

        // Display search results
        function displaySearchResults(patients) {
            hideLoading();
            paginationContainer.style.display = 'none';

            if (patients.length === 0) {
                tableBody.style.display = 'none';
                noResults.classList.remove('d-none');
                searchStatus.innerHTML = 'Found: <span class="text-danger">0</span> patient(s)';
                return;
            }

            // Clear and populate table
            tableBody.innerHTML = '';
            noResults.classList.add('d-none');
            tableBody.style.display = '';

            patients.forEach(patient => {
                const row = createPatientRow(patient);
                tableBody.appendChild(row);
            });

            searchStatus.innerHTML = `Found: <span class="text-success">${patients.length}</span> patient(s)`;

            // Reinitialize delete buttons
            initializeDeleteButtons();
        }

        // Create patient row HTML
        function createPatientRow(patient) {
            const tr = document.createElement('tr');
            tr.className = 'patient-row';

            const defaultProfile = patient.sex === 'male' ?
                '{{ asset('public/images/defaults/male.png') }}' :
                patient.sex === 'female' ?
                '{{ asset('public/images/defaults/female.png') }}' :
                '{{ asset('public/images/defaults/other.png') }}';

            const profileUrl = patient.profile_picture ?
                `/storage/${patient.profile_picture}` :
                defaultProfile;

            tr.innerHTML = `
                <td>
                    <img src="${profileUrl}" alt="${patient.full_name || 'Profile'}"
                        class="rounded-circle object-fit-cover border-primary border border-2"
                        style="width: 60px; height: 60px;">
                </td>
                <td>${patient.full_name || 'N/A'}</td>
                <td>${patient.mobile_no || 'N/A'}/<br>${patient.contact_no || 'N/A'}</td>
                <td>${patient.email || 'N/A'}</td>
                <td>${patient.full_address || 'N/A'}</td>
                <td class="text-end">
                    <a href="#" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#add-waitlist-modal">
                        <i class="fa-solid fa-hourglass-start"></i>
                    </a>
                    <form action="{{ route('specific-patient') }}" method="GET" class="d-inline">
                        <input type="hidden" name="patient_id" value="${patient.patient_id}">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </button>
                    </form>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#edit-patient-modal" data-id="${patient.patient_id}">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete-patient-btn"
                        data-id="${patient.patient_id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            return tr;
        }

        // Fallback: Search only current page
        function searchCurrentPage(searchTerm) {
            hideLoading();
            let visibleCount = 0;

            initialRows.forEach(row => {
                const name = row.dataset.searchName || '';

                const matches = name.includes(searchTerm);

                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            resultCount.textContent = visibleCount;
            paginationContainer.style.display = 'none';

            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
                tableBody.style.display = 'none';
            } else {
                noResults.classList.add('d-none');
                tableBody.style.display = '';
            }

            searchStatus.innerHTML =
                `Found: ${visibleCount} patient(s) <span class="text-warning">(current page only)</span>`;
        }

        // Restore pagination view
        function restorePaginationView() {
            isSearching = false;
            hideLoading();
            noResults.classList.add('d-none');
            tableBody.style.display = '';
            paginationContainer.style.display = '';
            searchStatus.innerHTML =
                'Total: <span id="result-count">{{ $patients->total() }}</span> patient(s)';

            // Restore original rows
            tableBody.innerHTML = '';
            initialRows.forEach(row => {
                row.style.display = '';
                tableBody.appendChild(row.cloneNode(true));
            });

            initializeDeleteButtons();
        }

        function showLoading() {
            tableBody.style.display = 'none';
            noResults.classList.add('d-none');
            searchLoading.classList.remove('d-none');
        }

        function hideLoading() {
            searchLoading.classList.add('d-none');
        }

        function initializeDeleteButtons() {
            document.querySelectorAll('.delete-patient-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const patientId = this.dataset.id;
                    document.getElementById('delete_patient_id').value = patientId;

                    const deleteModalEl = document.getElementById('delete-patient-modal');
                    const deleteModal = new bootstrap.Modal(deleteModalEl);
                    deleteModal.show();
                });
            });
        }

        // Event listeners
        searchInput.addEventListener('input', performSearch);

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            restorePaginationView();
            searchInput.focus();
        });

        // Initialize delete buttons for initial page
        initializeDeleteButtons();
    });
</script>

@if (!empty($patient))
    @include('pages.waitlist.modals.add')
    @include('pages.patients.modals.edit')
    @include('pages.patients.modals.delete')
@endif

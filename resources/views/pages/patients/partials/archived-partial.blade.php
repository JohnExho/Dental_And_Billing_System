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
                Total: <span id="result-count">{{ $patients->total() }}</span> archived patient(s)
            </span>
        </small>
    </div>

    @if ($patients->isEmpty())
        <p class="p-3 mb-0 text-warning text-center">
            <i class="bi bi-archive fs-3 d-block mb-2"></i>
            No archived patients found.
        </p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-striped table-warning" id="patients-table">
                <thead class="bg-warning">
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
                                <img src="{{ asset($profileUrl) }}"
                                     alt="{{ $patient->full_name ?? 'Profile' }}"
                                     class="rounded-circle object-fit-cover border-warning border border-2"
                                     style="width: 60px; height: 60px; opacity: 0.8;">
                            </td>
                            <td>{{ $patient->full_name }}</td>
                            <td>{{ $patient->mobile_no ?? 'N/A' }}/<br>{{ $patient->contact_no ?? 'N/A' }}</td>
                            <td>{{ $patient->email ?? 'N/A' }}</td>
                            <td>{{ $patient->full_address }}</td>

                            <td class="text-end">
                                <!-- Restore Button -->
                                <button type="button" 
                                        class="btn btn-outline-success btn-sm unarchive-patient-btn"
                                        data-id="{{ $patient->patient_id }}"
                                        data-name="{{ $patient->full_name }}"
                                        title="Restore Patient"
                                        onclick="event.stopPropagation();">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>

                                <!-- Delete Button -->
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm delete-patient-btn"
                                        data-id="{{ $patient->patient_id }}"
                                        data-name="{{ $patient->full_name }}"
                                        title="Permanently Delete"
                                        onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- No Results Message -->
            <div id="no-results" class="p-4 text-center text-muted d-none">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                <p class="mb-0">No archived patients found matching your search.</p>
            </div>

            <!-- Loading Spinner -->
            <div id="search-loading" class="p-4 text-center d-none">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 text-muted">Searching all archived patients...</p>
            </div>
        </div>

        <!-- Pagination (hidden during search) -->
        <div id="pagination-container" class="p-3">
            {{ $patients->links('vendor.pagination.bootstrap-5') }}
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

        let allPatients = [];
        let searchTimeout = null;
        let isSearching = false;

        const initialRows = Array.from(document.querySelectorAll('.patient-row'));

        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            clearTimeout(searchTimeout);

            if (searchTerm === '') {
                restorePaginationView();
                return;
            }

            searchTimeout = setTimeout(() => {
                fetchAndSearch(searchTerm);
            }, 300);
        }

        async function fetchAndSearch(searchTerm) {
            try {
                isSearching = true;
                showLoading();

                const response = await fetch('{{ route('patients.all') }}?archived=1', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch patients');

                allPatients = await response.json();

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

        function displaySearchResults(patients) {
            hideLoading();
            paginationContainer.style.display = 'none';

            if (patients.length === 0) {
                tableBody.style.display = 'none';
                noResults.classList.remove('d-none');
                searchStatus.innerHTML = 'Found: <span class="text-danger">0</span> archived patient(s)';
                return;
            }

            tableBody.innerHTML = '';
            noResults.classList.add('d-none');
            tableBody.style.display = '';

            patients.forEach(patient => {
                const row = createPatientRow(patient);
                tableBody.appendChild(row);
            });

            searchStatus.innerHTML = `Found: <span class="text-success">${patients.length}</span> archived patient(s)`;
            initializeActionButtons();
        }

        function createPatientRow(patient) {
            const tr = document.createElement('tr');
            tr.className = 'patient-row';
            tr.dataset.patientId = patient.patient_id;
            tr.dataset.searchName = (patient.full_name || '').toLowerCase();

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
                        class="rounded-circle object-fit-cover border-warning border border-2"
                        style="width: 60px; height: 60px; opacity: 0.8;">
                </td>
                <td>${patient.full_name || 'N/A'}</td>
                <td>${patient.mobile_no || 'N/A'}/<br>${patient.contact_no || 'N/A'}</td>
                <td>${patient.email || 'N/A'}</td>
                <td>${patient.full_address || 'N/A'}</td>
                <td class="text-end">
                    <button type="button" 
                            class="btn btn-outline-success btn-sm unarchive-patient-btn"
                            data-id="${patient.patient_id}"
                            data-name="${patient.full_name || 'N/A'}"
                            title="Restore Patient"
                            onclick="event.stopPropagation();">
                        <i class="bi bi-arrow-clockwise"></i> Restore
                    </button>
                    <button type="button" 
                            class="btn btn-outline-danger btn-sm delete-patient-btn"
                            data-id="${patient.patient_id}"
                            data-name="${patient.full_name || 'N/A'}"
                            title="Permanently Delete"
                            onclick="event.stopPropagation();">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </td>
            `;

            return tr;
        }

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
                `Found: ${visibleCount} archived patient(s) <span class="text-warning">(current page only)</span>`;
        }

        function restorePaginationView() {
            isSearching = false;
            hideLoading();
            noResults.classList.add('d-none');
            tableBody.style.display = '';
            paginationContainer.style.display = '';
            searchStatus.innerHTML =
                'Total: <span id="result-count">{{ $patients->total() }}</span> archived patient(s)';

            tableBody.innerHTML = '';
            initialRows.forEach(row => {
                row.style.display = '';
                tableBody.appendChild(row.cloneNode(true));
            });

            initializeActionButtons();
        }

        function showLoading() {
            tableBody.style.display = 'none';
            noResults.classList.add('d-none');
            searchLoading.classList.remove('d-none');
        }

        function hideLoading() {
            searchLoading.classList.add('d-none');
        }

        function initializeActionButtons() {
            // Unarchive buttons
            document.querySelectorAll('.unarchive-patient-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const patientId = this.dataset.id;
                    const patientName = this.dataset.name;
                    
                    // Check if modal elements exist
                    const unarchiveIdInput = document.getElementById('unarchive_patient_id');
                    const unarchiveNameSpan = document.getElementById('unarchive_patient_name');
                    const unarchiveModalEl = document.getElementById('unarchive-patient-modal');
                    
                    if (!unarchiveIdInput || !unarchiveNameSpan || !unarchiveModalEl) {
                        console.error('Unarchive modal elements not found');
                        alert('Error: Unarchive modal not properly loaded. Please refresh the page.');
                        return;
                    }
                    
                    unarchiveIdInput.value = patientId;
                    unarchiveNameSpan.textContent = patientName;

                    const unarchiveModal = new bootstrap.Modal(unarchiveModalEl);
                    unarchiveModal.show();
                });
            });

            // Delete buttons
            document.querySelectorAll('.delete-patient-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const patientId = this.dataset.id;
                    const patientName = this.dataset.name;
                    
                    // Check if modal elements exist
                    const deleteIdInput = document.getElementById('delete_patient_id');
                    const deleteNameSpan = document.getElementById('delete_patient_name');
                    const deleteModalEl = document.getElementById('delete-patient-modal');
                    
                    if (!deleteIdInput || !deleteNameSpan || !deleteModalEl) {
                        console.error('Delete modal elements not found');
                        alert('Error: Delete modal not properly loaded. Please refresh the page.');
                        return;
                    }
                    
                    deleteIdInput.value = patientId;
                    deleteNameSpan.textContent = patientName;

                    const deleteModal = new bootstrap.Modal(deleteModalEl);
                    deleteModal.show();
                });
            });
        }

        searchInput.addEventListener('input', performSearch);

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            restorePaginationView();
            searchInput.focus();
        });

        initializeActionButtons();
    });
</script>

@include('pages.patients.modals.archive')
@include('pages.patients.modals.delete')
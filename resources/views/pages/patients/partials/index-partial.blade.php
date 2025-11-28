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
                        <th class="sortable" data-sort="full_name" data-order="asc" style="cursor:pointer;">
                            Name <span class="sort-indicator"></span>
                        </th>
                        <th>Contact</th>
                        <th class="sortable" data-sort="email" data-order="asc" style="cursor:pointer;">
                            Email <span class="sort-indicator"></span>
                        </th>
                        <th class="sortable" data-sort="full_address" data-order="asc" style="cursor:pointer;">
                            Address <span class="sort-indicator"></span>
                        </th>
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
                                    data-barangay_name="{{ optional($patient->address->barangay)->name}}"
                                    data-profile_picture="{{ $patient->profile_picture ? asset('storage/' . $patient->profile_picture) : '' }}"
                                    data-weight="{{ $patient->weight }}" data-height="{{ $patient->height }}"
                                    data-school="{{ $patient->school }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button type="button" class="btn btn-outline-warning btn-sm archive-patient-btn"
                                    data-id="{{ $patient->patient_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-archive"></i>
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
            {{ $patients->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('patient-search');
        const clearBtn = document.getElementById('clear-search');
        const resultCount = document.getElementById('result-count');
        const searchStatus = document.getElementById('search-status');
        const tableBody = document.getElementById('patients-tbody');
        const noResults = document.getElementById('no-results');
        const searchLoading = document.getElementById('search-loading');
        const paginationContainer = document.getElementById('pagination-container');
        const patientsTable = document.getElementById('patients-table');

        // Sortable header elements
        const sortableHeaders = Array.from(document.querySelectorAll('th.sortable'));

        // State
        let allPatients = []; // fetched JSON (for searching)
        let initialRows = Array.from(document.querySelectorAll('.patient-row')); // Blade-loaded rows
        let searchTimeout = null;
        let isSearching = false;
        let currentSort = { column: null, order: 'asc' };

        // Utilities
        function textForColumnFromRow(row, column) {
            // Based on table layout:
            // 0 => profile, 1 => full_name, 2 => contact, 3 => email, 4 => address
            if (!row) return '';
            if (column === 'full_name') return (row.children[1]?.textContent || '').trim().toLowerCase();
            if (column === 'email') return (row.children[3]?.textContent || '').trim().toLowerCase();
            if (column === 'full_address') return (row.children[4]?.textContent || '').trim().toLowerCase();
            return '';
        }

        function compareStrings(a, b, order = 'asc') {
            if (a < b) return order === 'asc' ? -1 : 1;
            if (a > b) return order === 'asc' ? 1 : -1;
            return 0;
        }

        // Sorting for Blade DOM rows
        function sortAndRenderInitialRows() {
            const column = currentSort.column;
            const order = currentSort.order;

            // Clone rows so we don't mutate original NodeList references
            const rows = initialRows.map(r => r.cloneNode(true));

            rows.sort((rA, rB) => {
                const a = textForColumnFromRow(rA, column);
                const b = textForColumnFromRow(rB, column);
                return compareStrings(a, b, order);
            });

            // Replace table body contents with sorted rows
            tableBody.innerHTML = '';
            rows.forEach(r => tableBody.appendChild(r));
            initializeArchiveButtons(); // re-hook buttons
        }

        // Sorting for fetched JSON array
        function sortAllPatientsArray() {
            const column = currentSort.column;
            const order = currentSort.order;
            if (!column) return;

            allPatients.sort((a, b) => {
                const aVal = (a[column] ?? '').toString().toLowerCase();
                const bVal = (b[column] ?? '').toString().toLowerCase();
                return compareStrings(aVal, bVal, order);
            });
        }

        // Create patient row from JSON object (used for search results)
        function createPatientRowFromObject(patient) {
            const tr = document.createElement('tr');
            tr.className = 'patient-row';
            tr.dataset.patientId = patient.patient_id;
            tr.dataset.searchName = (patient.full_name || '').toLowerCase();
            tr.dataset.searchContact = ((patient.mobile_no || '') + ' ' + (patient.contact_no || '')).toLowerCase();
            tr.dataset.searchEmail = (patient.email || '').toLowerCase();
            tr.dataset.searchAddress = (patient.full_address || '').toLowerCase();

            const defaultProfile = (patient.sex === 'male')
                ? '{{ asset("public/images/defaults/male.png") }}'
                : (patient.sex === 'female')
                    ? '{{ asset("public/images/defaults/female.png") }}'
                    : '{{ asset("public/images/defaults/other.png") }}';

            const profileUrl = patient.profile_picture ? `/storage/${patient.profile_picture}` : defaultProfile;

            tr.innerHTML = `
                <td>
                    <img src="${profileUrl}" alt="${(patient.full_name || 'Profile')}"
                        class="rounded-circle object-fit-cover border-primary border border-2"
                        style="width: 60px; height: 60px;">
                </td>
                <td>${patient.full_name || 'N/A'}</td>
                <td>${patient.mobile_no || 'N/A'}/<br>${patient.contact_no || 'N/A'}</td>
                <td>${patient.email || 'N/A'}</td>
                <td>${patient.full_address || 'N/A'}</td>
                <td class="text-end">
                    <button type="button" 
                        class="btn btn-outline-dark btn-sm" 
                        data-bs-toggle="modal"
                        data-bs-target="#add-waitlist-modal"
                        data-patient-id="${patient.patient_id}">
                        <i class="fa-solid fa-hourglass-start"></i>
                    </button>

                    <form action="{{ route('specific-patient') }}" method="GET" class="d-inline">
                        <input type="hidden" name="patient_id" value="${patient.patient_id}">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </button>
                    </form>

                    <button type="button" class="btn btn-sm btn-outline-secondary edit-patient-btn"
                        data-bs-toggle="modal" data-bs-target="#edit-patient-modal"
                        data-id="${patient.patient_id}">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <button type="button" class="btn btn-outline-warning btn-sm archive-patient-btn"
                        data-id="${patient.patient_id}" onclick="event.stopPropagation();">
                        <i class="bi bi-archive"></i>
                    </button>
                </td>
            `;
            return tr;
        }

        // Display search results (array of patient objects)
        function displaySearchResults(patients) {
            hideLoading();
            paginationContainer.style.display = 'none';

            if (!patients || patients.length === 0) {
                tableBody.style.display = 'none';
                noResults.classList.remove('d-none');
                searchStatus.innerHTML = 'Found: <span class="text-danger">0</span> patient(s)';
                return;
            }

            // If a column sort is active, sort the results first
            if (currentSort.column) {
                // Ensure the array is sorted before rendering
                // Work on a copy to avoid side-effects if needed
                patients = patients.slice();
                patients.sort((a, b) => {
                    const aVal = (a[currentSort.column] ?? '').toString().toLowerCase();
                    const bVal = (b[currentSort.column] ?? '').toString().toLowerCase();
                    return compareStrings(aVal, bVal, currentSort.order);
                });
            }

            // Clear and populate table
            tableBody.innerHTML = '';
            noResults.classList.add('d-none');
            tableBody.style.display = '';

            patients.forEach(patient => {
                const row = createPatientRowFromObject(patient);
                tableBody.appendChild(row);
            });

            searchStatus.innerHTML = `Found: <span class="text-success">${patients.length}</span> patient(s)`;

            initializeArchiveButtons();
        }

        // Debounced search function
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

        // Fetch all patients and search (AJAX)
        async function fetchAndSearch(searchTerm) {
            try {
                isSearching = true;
                showLoading();

                const response = await fetch('{{ route('patients.all') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch patients');

                allPatients = await response.json();

                // Filter by name (you can expand to other fields if needed)
                let filtered = allPatients.filter(patient => {
                    const name = (patient.full_name || '').toString().toLowerCase();
                    return name.includes(searchTerm);
                });

                // Display (displaySearchResults will apply currentSort if set)
                displaySearchResults(filtered);

            } catch (error) {
                console.error('Search error:', error);
                // Fallback to current page search if AJAX fails
                searchCurrentPage(searchTerm);
            }
        }

        // Fallback: search only current (Blade) page rows
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

        // Restore original pagination view and rows
        function restorePaginationView() {
            isSearching = false;
            hideLoading();
            noResults.classList.add('d-none');
            tableBody.style.display = '';
            paginationContainer.style.display = '';
            searchStatus.innerHTML =
                'Total: <span id="result-count">{{ $patients->total() }}</span> patient(s)';

            // Restore original rows (clone to detach any dynamically added listeners)
            tableBody.innerHTML = '';
            initialRows.forEach(row => {
                row.style.display = '';
                tableBody.appendChild(row.cloneNode(true));
            });

            initializeArchiveButtons();
        }

        // UI helpers
        function showLoading() {
            tableBody.style.display = 'none';
            noResults.classList.add('d-none');
            searchLoading.classList.remove('d-none');
        }

        function hideLoading() {
            searchLoading.classList.add('d-none');
        }

        // Archive button modal wiring
        function initializeArchiveButtons() {
            document.querySelectorAll('.archive-patient-btn').forEach(btn => {
                btn.removeEventListener('click', handleArchiveClick);
                btn.addEventListener('click', handleArchiveClick);
            });
        }

        function handleArchiveClick(e) {
            e.stopPropagation();
            const patientId = this.dataset.id;
            const input = document.getElementById('archive_patient_id');
            if (input) input.value = patientId;

            const archiveModalEl = document.getElementById('archive-patient-modal');
            const archiveModal = new bootstrap.Modal(archiveModalEl);
            archiveModal.show();
        }

        // Sorting header click wiring
        function clearSortIndicators() {
            sortableHeaders.forEach(h => {
                const span = h.querySelector('.sort-indicator');
                if (span) span.textContent = '';
                h.dataset.order = 'asc';
            });
        }

        function setSortIndicator(header, order) {
            clearSortIndicators();
            const span = header.querySelector('.sort-indicator');
            if (span) span.textContent = order === 'asc' ? '▲' : '▼';
            header.dataset.order = order;
        }

        sortableHeaders.forEach(header => {
            header.addEventListener('click', function () {
                const column = this.dataset.sort;
                const prevOrder = this.dataset.order === 'asc' ? 'asc' : 'desc';
                const newOrder = prevOrder === 'asc' ? 'desc' : 'asc';

                currentSort = { column, order: newOrder };
                setSortIndicator(this, newOrder);

                if (isSearching) {
                    // we're showing search results; sort the fetched array & re-render
                    sortAllPatientsArray();
                    // filter by current search term to display only matching
                    const term = searchInput.value.toLowerCase().trim();
                    const filtered = (allPatients || []).filter(p => (p.full_name || '').toLowerCase().includes(term));
                    displaySearchResults(filtered);
                } else {
                    // sort the initial (paged) rows and render
                    sortAndRenderInitialRows();
                }
            });
        });

        // Events
        searchInput.addEventListener('input', performSearch);

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            restorePaginationView();
            searchInput.focus();
        });

        // Initialize
        initializeArchiveButtons();

        // Make sure resultCount shows correct initial count (in case Blade changed)
        resultCount.textContent = '{{ $patients->total() }}';
    });
</script>

@if (!empty($patient))
    @include('pages.waitlist.modals.add')
    @include('pages.patients.modals.edit')
    @include('pages.patients.modals.archive')
@endif

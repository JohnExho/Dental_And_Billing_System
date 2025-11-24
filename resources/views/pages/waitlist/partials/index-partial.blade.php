<div class="card-body p-0">
    <!-- Search Input -->
    <div class="p-3 bg-light border-bottom">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" 
                   id="waitlist-search" 
                   class="form-control" 
                   placeholder="Search by patient name (e.g., Bins, Ignatius)">
            <button class="btn btn-outline-secondary" type="button" id="clear-search">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <small class="text-muted d-block mt-2">
            <span id="search-status">
                Total: <span id="result-count">{{ $waitlist->total() }}</span> patient(s)
            </span>
        </small>
    </div>

    @if ($waitlist->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">
            {{ 'No Waitlist found. Add one using the button above.' }}
        </p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-striped table-primary">
                <thead class="table-primary">
                    <tr>
                        <th>Profile Picture</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Associate</th>
                        <th>Queue</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="waitlist-tbody">
                    @forelse ($waitlist as $wl)
                        @php
                            // Decide which image to show
                            $defaultProfile = match ($wl->patient?->sex) {
                                'male' => asset('public/images/defaults/male.png'),
                                'female' => asset('public/images/defaults/female.png'),
                                default => asset('public/images/defaults/other.png'),
                            };

                            $profileUrl = $wl->patient?->profile_picture
                                ? Storage::url($wl->patient?->profile_picture)
                                : $defaultProfile;
                        @endphp

                        <tr class="waitlist-row"
                            data-waitlist-id="{{ $wl->waitlist_id }}"
                            data-search-name="{{ strtolower($wl->patient?->full_name ?? '') }}">
                            <td>
                                <img src="{{ $profileUrl }}" alt="{{ $wl->patient?->full_name ?? 'Profile' }}"
                                    class="rounded-circle object-fit-cover border-primary border border-2"
                                    style="width: 60px; height: 60px;">
                            </td>
                            <td>{{ $wl->patient?->full_name }}</td>
                            <td>{{ $wl->patient?->email }}</td>
                            <td>{{ $wl->associate?->full_name ?? 'N/A' }}</td>
                            <td>{{ $wl->queue_position ?? 'N/A' }}</td>
                            <td>
                                @if ($wl->status === 'waiting')
                                    <span class="badge bg-info">Waiting</span>
                                @elseif ($wl->status === 'in_consultation')
                                    <span class="badge bg-warning">In Consultation</span>
                                @elseif ($wl->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif ($wl->status === 'cancelled')
                                    <span class="badge bg-warning">Cancelled</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-waitlist-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $wl->waitlist_id }}" data-associate_id="{{ $wl->associate_id }}"
                                    data-laboratory_id="{{ $wl->laboratory_id }}" data-status="{{ $wl->status }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button type="button" class="btn btn-outline-danger btn-sm delete-wl-btn"
                                    data-id="{{ $wl->waitlist_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-warning">
                                No waitlist found.
                            </td>
                        </tr>
                    @endforelse
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
                <p class="mt-2 mb-0 text-muted">Searching all waitlist...</p>
            </div>
        </div>

        <!-- Pagination (hidden during search) -->
        <div id="pagination-container" class="p-3">
            {{ $waitlist->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('waitlist-search');
        const clearBtn = document.getElementById('clear-search');
        const resultCount = document.getElementById('result-count');
        const searchStatus = document.getElementById('search-status');
        const tableBody = document.getElementById('waitlist-tbody');
        const noResults = document.getElementById('no-results');
        const searchLoading = document.getElementById('search-loading');
        const paginationContainer = document.getElementById('pagination-container');
        
        let allWaitlist = [];
        let searchTimeout = null;
        let isSearching = false;

        // Initial waitlist rows from current page
        const initialRows = Array.from(document.querySelectorAll('.waitlist-row'));

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

        // Fetch all waitlist and search
        async function fetchAndSearch(searchTerm) {
            try {
                isSearching = true;
                showLoading();

                // Fetch all waitlist entries
                const response = await fetch('{{ route("waitlist.all") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch waitlist');

                allWaitlist = await response.json();
                
                // Filter waitlist based on search term (name only)
                const filtered = allWaitlist.filter(wl => {
                    const name = (wl.patient_name || '').toLowerCase();
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
        function displaySearchResults(waitlistData) {
            hideLoading();
            paginationContainer.style.display = 'none';

            if (waitlistData.length === 0) {
                tableBody.style.display = 'none';
                noResults.classList.remove('d-none');
                searchStatus.innerHTML = 'Found: <span class="text-danger">0</span> patient(s)';
                return;
            }

            // Clear and populate table
            tableBody.innerHTML = '';
            noResults.classList.add('d-none');
            tableBody.style.display = '';

            waitlistData.forEach(wl => {
                const row = createWaitlistRow(wl);
                tableBody.appendChild(row);
            });

            searchStatus.innerHTML = `Found: <span class="text-success">${waitlistData.length}</span> patient(s)`;

            // Reinitialize delete buttons
            initializeDeleteButtons();
        }

        // Create waitlist row HTML
        function createWaitlistRow(wl) {
            const tr = document.createElement('tr');
            tr.className = 'waitlist-row';
            
            const defaultProfile = wl.patient_sex === 'male' 
                ? '{{ asset("public/images/defaults/male.png") }}'
                : wl.patient_sex === 'female'
                ? '{{ asset("public/images/defaults/female.png") }}'
                : '{{ asset("public/images/defaults/other.png") }}';
            
            const profileUrl = wl.profile_picture 
                ? `/storage/${wl.profile_picture}`
                : defaultProfile;

            let statusBadge = '';
            if (wl.status === 'waiting') {
                statusBadge = '<span class="badge bg-info">Waiting</span>';
            } else if (wl.status === 'in_consultation') {
                statusBadge = '<span class="badge bg-warning">In Consultation</span>';
            } else if (wl.status === 'completed') {
                statusBadge = '<span class="badge bg-success">Completed</span>';
            }

            tr.innerHTML = `
                <td>
                    <img src="${profileUrl}" alt="${wl.patient_name || 'Profile'}"
                        class="rounded-circle object-fit-cover border-primary border border-2"
                        style="width: 60px; height: 60px;">
                </td>
                <td>${wl.patient_name || 'N/A'}</td>
                <td>${wl.patient_email || 'N/A'}</td>
                <td>${wl.associate_name || 'N/A'}</td>
                <td>${wl.queue_position || 'N/A'}</td>
                <td>${statusBadge}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#edit-waitlist-modal"
                        data-id="${wl.waitlist_id}" 
                        data-associate_id="${wl.associate_id || ''}"
                        data-laboratory_id="${wl.laboratory_id || ''}" 
                        data-status="${wl.status}">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm delete-wl-btn"
                        data-id="${wl.waitlist_id}">
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

            searchStatus.innerHTML = `Found: ${visibleCount} patient(s) <span class="text-warning">(current page only)</span>`;
        }

        // Restore pagination view
        function restorePaginationView() {
            isSearching = false;
            hideLoading();
            noResults.classList.add('d-none');
            tableBody.style.display = '';
            paginationContainer.style.display = '';
            searchStatus.innerHTML = 'Total: <span id="result-count">{{ $waitlist->total() }}</span> patient(s)';

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
            document.querySelectorAll('.delete-wl-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const wlId = this.dataset.id;
                    document.getElementById('delete_waitlist_id').value = wlId;

                    const deleteModalEl = document.getElementById('delete-waitlist-modal');
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

@foreach ($waitlist as $wl)
    @include('pages.waitlist.modals.edit', ['waitlist' => $wl])
@endforeach
@include('pages.waitlist.modals.delete')
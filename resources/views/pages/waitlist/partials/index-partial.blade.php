<style>
    .pagination-container nav {
        display: flex !important;
        align-items: center;
        justify-content: center !important;
    }

    .pagination {
        justify-content: center !important;
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sort-icon {
        margin-left: 5px;
    }
</style>

<div class="card-body p-0">
    <!-- Search Input -->
    <div class="p-3 bg-light border-bottom">
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" id="waitlist-search" class="form-control" placeholder="Search by patient name (e.g., Bins, Ignatius)">
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
                        <th class="sortable" data-column="patient_name" data-order="asc">
                            Name <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="sortable" data-column="patient_email" data-order="asc">
                            Email <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th>Associate</th>
                        <th>Queue</th>
                        <th class="sortable" data-column="patient_address" data-order="asc">
                            Address <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="waitlist-tbody">
                    @foreach ($waitlist as $wl)
                        @php
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
                            data-patient-name="{{ strtolower($wl->patient?->full_name ?? '') }}"
                            data-patient-email="{{ strtolower($wl->patient?->email ?? '') }}"
                            data-patient-address="{{ strtolower($wl->patient?->full_address ?? '') }}">
                            <td>
                                <img src="{{ $profileUrl }}" alt="{{ $wl->patient?->full_name ?? 'Profile' }}"
                                    class="rounded-circle object-fit-cover border-primary border border-2"
                                    style="width: 60px; height: 60px;">
                            </td>
                            <td>{{ $wl->patient?->full_name }}</td>
                            <td>{{ $wl->patient?->email }}</td>
                            <td>{{ $wl->associate?->full_name ?? 'N/A' }}</td>
                            <td>{{ $wl->queue_position ?? 'N/A' }}</td>
                            <td>{{ $wl->patient?->full_address ?? 'N/A' }}</td>
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
                                    data-bs-target="#edit-waitlist-modal"
                                    data-id="{{ $wl->waitlist_id }}"
                                    data-associate_id="{{ $wl->associate_id }}"
                                    data-status="{{ $wl->status }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm delete-wl-btn"
                                    data-id="{{ $wl->waitlist_id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="no-results" class="p-4 text-center text-muted d-none">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                <p class="mb-0">No patients found matching your search.</p>
            </div>

            <div id="search-loading" class="p-4 text-center d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0 text-muted">Searching all waitlist...</p>
            </div>
        </div>

        <div id="pagination-container" class="p-3 d-flex justify-content-center">
            {{ $waitlist->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('waitlist-search');
        const clearBtn = document.getElementById('clear-search');
        const tableBody = document.getElementById('waitlist-tbody');
        const initialRows = Array.from(document.querySelectorAll('.waitlist-row'));
        let currentSort = { column: null, order: 'asc' };

        function sortTable(column, order) {
            const rows = Array.from(tableBody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                const valA = (a.dataset[column] || '').toLowerCase(); // column should be camelCase
                const valB = (b.dataset[column] || '').toLowerCase();
                if (valA < valB) return order === 'asc' ? -1 : 1;
                if (valA > valB) return order === 'asc' ? 1 : -1;
                return 0;
            });
            rows.forEach(row => tableBody.appendChild(row));
        }

        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                // Convert column to camelCase
                const column = header.dataset.column.replace(/_([a-z])/g, (_, l) => l.toUpperCase());
                const order = header.dataset.order === 'asc' ? 'desc' : 'asc';
                header.dataset.order = order;
                sortTable(column, order);
            });
        });


        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            tableBody.innerHTML = '';
            initialRows.forEach(row => tableBody.appendChild(row.cloneNode(true)));
        });
    });
</script>

@include('pages.waitlist.modals.edit')
@include('pages.waitlist.modals.delete')

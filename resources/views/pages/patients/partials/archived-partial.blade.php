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

</script>

@include('pages.patients.modals.archive')
@include('pages.patients.modals.delete')
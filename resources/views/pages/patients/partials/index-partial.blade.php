<div class="card-body p-0">

    <!-- Search Input -->
    <div class="p-3 bg-light border-bottom">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" id="patient-search" class="form-control"
                placeholder="Search archived patients...">
            <button class="btn btn-outline-secondary" type="button" id="clear-search">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <small class="text-muted d-block mt-2">
            <span id="search-status">
                Archived Total: <span id="result-count">{{ $patients->total() }}</span>
            </span>
        </small>
    </div>

    @if ($patients->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No archived patients found.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-striped table-warning" id="patients-table">
                <thead class="bg-secondary text-white">
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

                        <tr class="patient-row"
                            data-search-name="{{ strtolower($patient->full_name) }}"
                            data-search-contact="{{ strtolower($patient->mobile_no ?? '') }} {{ strtolower($patient->contact_no ?? '') }}"
                            data-search-email="{{ strtolower($patient->email ?? '') }}"
                            data-search-address="{{ strtolower($patient->full_address) }}">

                            <td>
                                <img src="{{ $profileUrl }}"
                                     class="rounded-circle object-fit-cover border border-2 border-danger"
                                     style="width: 60px; height: 60px;">
                            </td>

                            <td>{{ $patient->full_name }}</td>
                            <td>{{ $patient->mobile_no ?? 'N/A' }}/<br>{{ $patient->contact_no ?? 'N/A' }}</td>
                            <td>{{ $patient->email ?? 'N/A' }}</td>
                            <td>{{ $patient->full_address }}</td>

                            <td class="text-end">
                                <!-- Restore Button -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-success restore-patient-btn"
                                        data-id="{{ $patient->patient_id }}">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- No Results -->
            <div id="no-results" class="p-4 text-center text-muted d-none">
                <i class="bi bi-search fs-1 mb-2"></i>
                <p>No archived patients match your search.</p>
            </div>

            <!-- Loading -->
            <div id="search-loading" class="p-4 text-center d-none">
                <div class="spinner-border text-warning"></div>
                <p class="mt-2 text-muted">Searching archived patients...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination-container" class="p-3">
            {{ $patients->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function initializeRestoreButtons() {
            document.querySelectorAll('.restore-patient-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    document.getElementById('restore_patient_id').value = id;

                    const modal = new bootstrap.Modal(document.getElementById('restore-patient-modal'));
                    modal.show();
                });
            });
        }

        initializeRestoreButtons();
    });
</script>

{{-- Include Restore Modal --}}
@include('pages.patients.modals.restore')
@include('pages.patients.modals.delete')
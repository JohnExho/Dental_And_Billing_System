<style>
    .sortable {
        cursor: pointer;
        user-select: none;
    }
    .sort-icon {
        margin-left: 5px;
    }
</style>
<div class="card-body p-0">
    @if ($staffs->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">
            {{ session('clinic_id')
                ? 'No staffs found for this clinic. Add one using the button above.'
                : 'No staffs available globally.' }}
        </p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-column="name" data-order="asc">
                            Name <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="sortable" data-column="email" data-order="asc">
                            Email <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th>
                            Mobile
                        </th>
                        <th>Contact</th>
                        <th class="sortable" data-column="address" data-order="asc">
                            Address <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="staffs-tbody">
                    @foreach ($staffs as $staff)
                        @php
                            $fullAddress = trim(
                                ($staff->address->house_no ?? '') . ' ' .
                                ($staff->address->street ?? '') . ' ' .
                                ($staff->address->barangay->name ?? '') . ' ' .
                                ($staff->address->city->name ?? '') . ' ' .
                                ($staff->address->province->name ?? '')
                            );
                        @endphp
                        <tr 
                            data-name="{{ strtolower($staff->full_name) }}"
                            data-email="{{ strtolower($staff->email) }}"
                            data-mobile="{{ strtolower($staff->mobile_no ?? '') }}"
                            data-address="{{ strtolower($fullAddress) }}"
                        >
                            <td>{{ $staff->full_name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ $staff->mobile_no ?? 'N/A' }}</td>
                            <td>{{ $staff->contact_no ?? 'N/A' }}</td>
                            <td>{{ $fullAddress }}</td>
                            <td>
                                @if ($staff->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <!-- Info Button -->
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#staff-detail-modal"
                                    data-first-name="{{ $staff->first_name }}"
                                    data-middle-name="{{ $staff->middle_name }}"
                                    data-last-name="{{ $staff->last_name }}"
                                    data-email="{{ $staff->email }}"
                                    data-contact="{{ $staff->contact_no }} / {{ $staff->mobile_no }}"
                                    data-address="{{ $fullAddress }}">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-staff-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $staff->account_id }}"
                                    data-first_name="{{ $staff->first_name }}"
                                    data-middle_name="{{ $staff->middle_name }}"
                                    data-last_name="{{ $staff->last_name }}"
                                    data-email="{{ $staff->email }}"
                                    data-contact_no="{{ $staff->contact_no }}"
                                    data-mobile_no="{{ $staff->mobile_no }}"
                                    data-house_no="{{ optional($staff->address)->house_no }}"
                                    data-street="{{ optional($staff->address)->street }}"
                                    data-province_id="{{ optional($staff->address->province)->province_id }}"
                                    data-province_name="{{ optional($staff->address->province)->name }}"
                                    data-city_id="{{ optional($staff->address->city)->city_id }}"
                                    data-city_name="{{ optional($staff->address->city)->name }}"    
                                    data-barangay_id="{{ optional($staff->address)->barangay_id }}"
                                    data-barangay_name="{{ optional($staff->address->barangay)->name }}"
                                    data-is_active="{{ $staff->is_active }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-outline-danger btn-sm delete-staff-btn"
                                    data-id="{{ $staff->account_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('staffs-tbody');

    // Sorting
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', () => {
            const column = header.dataset.column;
            const order = header.dataset.order === 'asc' ? 'desc' : 'asc';
            header.dataset.order = order;

            const rows = Array.from(tableBody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                const valA = (a.dataset[column] || '').toLowerCase();
                const valB = (b.dataset[column] || '').toLowerCase();
                if (valA < valB) return order === 'asc' ? -1 : 1;
                if (valA > valB) return order === 'asc' ? 1 : -1;
                return 0;
            });
            rows.forEach(row => tableBody.appendChild(row));
        });
    });

    // Delete buttons
    document.querySelectorAll('.delete-staff-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const staffId = this.dataset.id;
            document.getElementById('delete_staff_id').value = staffId;
            const deleteModalEl = document.getElementById('delete-staff-modal');
            const deleteModal = new bootstrap.Modal(deleteModalEl);
            deleteModal.show();
        });
    });
});
</script>

@include('pages.staffs.modals.info')
@include('pages.staffs.modals.edit')
@include('pages.staffs.modals.delete')

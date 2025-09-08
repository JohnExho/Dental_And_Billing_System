<div class="card-body p-0">
    @if ($staffs->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No staff found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staffs as $staff)
                        <tr>
                            <td>{{ $staff->full_name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ $staff->mobile_no ?? 'N/A' }}</td>
                            <td>{{ $staff->contact_no ?? 'N/A' }}</td>
                            <td>
                                {{ optional($staff->address)->house_no }} {{ optional($staff->address)->street }}<br>
                                {{ optional($staff->address->barangay)->name ?? '' }}
                                {{ optional($staff->address->city)->name ?? '' }}
                                {{ optional($staff->address->province)->name ?? '' }}
                            </td>
                            <td class="text-end">
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#staff-detail-modal" data-first-name="{{ $staff->first_name }}"
                                    data-middle-name="{{ $staff->middle_name }}"
                                    data-last-name="{{ $staff->last_name }}" data-email="{{ $staff->email }}"
                                    data-contact="{{ $staff->contact_no }} / {{ $staff->mobile_no }}"
                                    data-address="{{ optional($staff->address)->house_no }} {{ optional($staff->address)->street }} {{ optional($staff->address->barangay)->name ?? '' }} {{ optional($staff->address->city)->name ?? '' }} {{ optional($staff->address->province)->name ?? '' }}">
                                    <i class="bi bi-eye"></i>
                                </a>



                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-staff-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $staff->account_id }}" data-first_name="{{ $staff->first_name }}"
                                    data-middle_name="{{ $staff->middle_name }}"
                                    data-last_name="{{ $staff->last_name }}" data-email="{{ $staff->email }}"
                                    data-contact_no="{{ $staff->contact_no }}"
                                    data-mobile_no="{{ $staff->mobile_no }}"
                                    data-house_no="{{ optional($staff->address)->house_no }}"
                                    data-street="{{ optional($staff->address)->street }}"
                                    data-province_id="{{ optional($staff->address->province)->province_id }}"
                                    data-province_name="{{ optional($staff->address->province)->name }}"
                                    data-city_id="{{ optional($staff->address->city)->city_id }}"
                                    data-city_name="{{ optional($staff->address->city)->name }}"
                                    data-barangay_id="{{ optional($staff->address->barangay)->barangay_id }}"
                                    data-barangay_name="{{ optional($staff->address->barangay)->name }}">
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

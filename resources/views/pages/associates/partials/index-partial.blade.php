<div class="card-body p-0">
    @if ($associates->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">
            {{ session('clinic_id')
                ? 'No associates found for this clinic. Add one using the button above.'
                : 'No associates available globally.' }}
        </p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialty</th>
                        <th>Clinic</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($associates as $associate)
                        <tr>
                            <td>{{ $associate->full_name }}</td>
                            <td>{{ $associate->email }}</td>
                            <td>{{ $associate->specialty }}</td>
                            <td>{{ $associate->clinic->name ?? 'No Clinic' }}</td>
                            <td>
                                <i class="bi bi-telephone-fill me-1"></i>{{ $associate->contact_no }}<br>
                                <i class="bi bi-phone-fill me-1"></i>{{ $associate->mobile_no }}
                            </td>
                                <td>
                                    {{ optional($associate->address)->house_no }}
                                    {{ optional($associate->address)->street }}<br>
                                    {{ optional($associate->address->barangay)->name ?? '' }}
                                    {{ optional($associate->address->city)->name ?? '' }}
                                    {{ optional($associate->address->province)->name ?? '' }}
                                </td>
                            <td>
                                @if ($associate->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#associate-detail-modal"
                                    data-first-name="{{ $associate->first_name }}"
                                    data-middle-name="{{ $associate->middle_name }}"
                                    data-last-name="{{ $associate->last_name }}" data-email="{{ $associate->email }}"
                                    data-contact="{{ $associate->contact_no }} / {{ $associate->mobile_no }}"
                                    data-specialty="{{ $associate->specialty }}"
                                    data-address="{{ optional($associate->address)->house_no }} {{ optional($associate->address)->street }} {{ optional($associate->address->barangay)->name ?? '' }} {{ optional($associate->address->city)->name ?? '' }} {{ optional($associate->address->province)->name ?? '' }}">
                                    <i class="bi bi-eye"></i>
                                </a>



                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-associate-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $associate->associate_id }}"
                                    data-first_name="{{ $associate->first_name }}"
                                    data-middle_name="{{ $associate->middle_name }}"
                                    data-last_name="{{ $associate->last_name }}" data-email="{{ $associate->email }}"
                                    data-contact_no="{{ $associate->contact_no }}"
                                    data-mobile_no="{{ $associate->mobile_no }}"
                                    data-house_no="{{ optional($associate->address)->house_no }}"
                                    data-street="{{ optional($associate->address)->street }}"
                                    data-province_id="{{ optional($associate->address->province)->province_id }}"
                                    data-province_name="{{ optional($associate->address->province)->name }}"
                                    data-city_id="{{ optional($associate->address->city)->city_id }}"
                                    data-city_name="{{ optional($associate->address->city)->name }}"
                                    data-barangay_id="{{ optional($associate->address)->barangay_id }}"
                                    data-barangay_name="{{ optional($associate->address->barangay)->name }}"
                                    data-is_active="{{ $associate->is_active }}"
                                    data-specialty="{{ $associate->specialty }}"
                                    >
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-outline-danger btn-sm delete-associate-btn"
                                    data-id="{{ $associate->associate_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-warning">
                                No associates found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-associate-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const associateId = this.dataset.id;
                document.getElementById('delete_associate_id').value = associateId;

                const deleteModalEl = document.getElementById('delete-associate-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>
@include('pages.associates.modals.info')
@include('pages.associates.modals.edit')
@include('pages.associates.modals.delete')

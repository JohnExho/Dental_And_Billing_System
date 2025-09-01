{{-- resources/views/pages/clinics/partials/index-partial.blade.php --}}

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <a href="#" class="btn btn-light btn-sm float-end" data-bs-toggle="modal" data-bs-target="#add-clinic-modal">
            <i class="bi bi-plus-circle"></i> Add Clinic
        </a>
    </div>
    <div class="card-body p-0">
        @if($clinics->isEmpty())
            <p class="p-3 mb-0 text-danger text-center">No clinics found. Add one using the button above.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Schedule</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinics as $clinic)
                            <tr>
                                <td class="fw-semibold">{{ $clinic->name }}</td>
                                <td>{{ Str::limit($clinic->description, 50) ?? 'No Description Given' }}</td>
                                <td>{{ $clinic->schedule_summary ?? 'N/A' }}</td>
                                <td>
                                    {{ optional($clinic->address)->house_no }} {{ optional($clinic->address)->street }}<br>
                                    {{ optional($clinic->address->barangay)->name ?? '' }}
                                    {{ optional($clinic->address->city)->name ?? '' }}
                                    {{ optional($clinic->address->province)->name ?? '' }}
                                </td>
                                <td>{{ $clinic->email }}</td>
<td>
    <i class="bi bi-telephone-fill me-1"></i>{{ $clinic->contact_no }}<br>
    <i class="bi bi-phone-fill me-1"></i>{{ $clinic->mobile_no }}
</td>                                <td class="text-end">
                                    <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#clinic-detail-modal" data-name="{{ $clinic->name }}"
                                        data-description="{{ $clinic->description }}" data-email="{{ $clinic->email }}"
                                        data-contact="{{ $clinic->contact_no }} / {{ $clinic->mobile_no }}"
                                        data-address="{{ optional($clinic->address)->house_no }} {{ optional($clinic->address)->street }} {{ optional($clinic->address->barangay)->name ?? '' }} {{ optional($clinic->address->city)->name ?? '' }} {{ optional($clinic->address->province)->name ?? '' }}"
                                        data-house_no="{{ optional($clinic->address)->house_no }}"
                                        data-street="{{ optional($clinic->address)->street }}"
                                        data-schedule="{{ $clinic->schedule_summary ?? 'N/A' }}"
                                        data-schedules='@json($clinic->clinicSchedules)'>
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#edit-clinic-modal" onclick="event.stopPropagation();"
                                        data-id="{{ $clinic->clinic_id }}" data-name="{{ $clinic->name }}"
                                        data-description="{{ $clinic->description }}" data-specialty="{{ $clinic->specialty }}"
                                        data-email="{{ $clinic->email }}" data-contact_no="{{ $clinic->contact_no }}"
                                        data-mobile_no="{{ $clinic->mobile_no }}"
                                        data-house_no="{{ optional($clinic->address)->house_no }}"
                                        data-street="{{ optional($clinic->address)->street }}"
                                        data-province_id="{{ optional($clinic->address->province)->province_id }}"
                                        data-city_id="{{ optional($clinic->address->city)->city_id }}"
                                        data-barangay_id="{{ optional($clinic->address->barangay)->barangay_id }}"
                                        data-schedule_summary="{{ $clinic->schedule_summary ?? 'N/A' }}"
                                        data-schedules='@json($clinic->clinicSchedules)'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-clinic-btn"
                                        data-id="{{ $clinic->clinic_id }}" onclick="event.stopPropagation();">
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
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-clinic-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const clinicId = this.dataset.id;
                document.getElementById('delete_clinic_id').value = clinicId;

                const deleteModalEl = document.getElementById('delete-clinic-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });

</script>
@include('pages.clinics.modals.info')
@include('pages.clinics.modals.edit')
@include('pages.clinics.modals.delete')
<div class="card-body p-0">
    @if ($patients->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No patient found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Profile Pic</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                        <tr>
                            <td>
                                <img src="{{ $patient->profile_picture_url ?? 'https://placehold.co/40' }}"
                                    alt="Profile Picture" class="rounded-circle" width="40" height="40">
                            </td>
                            <td>{{ $patient->full_name }}</td>
                            <td>{{ $patient->mobile_no ?? 'N/A' }}/<br>{{ $patient->contact_no ?? 'N/A' }}</td>
                            <td>{{$patient->email ?? 'N/A'}}</td>
                            <td>{{$patient->date_of_birth ?? 'N/A'}}</td>

                            <td class="text-end">
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#patient-detail-modal" data-name="{{ $patient->name }}"
                                    data-description="{{ $patient->description }}"
                                    data-default_price="{{ $patient->default_price }}">
                                    <i class="bi bi-eye"></i>
                                </a>




                                {{-- ✅ Edit Button --}}
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-patient-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $patient->patient_id }}" data-name="{{ $patient->name }}"
                                    data-description="{{ $patient->description }}"
                                    data-default_price="{{ $patient->default_price }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- ✅ Delete Button --}}
                                <button type="button" class="btn btn-outline-danger btn-sm delete-patient-btn"
                                    data-id="{{ $patient->patient_id }}" onclick="event.stopPropagation();">
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
        document.querySelectorAll('.delete-patient-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const patientId = this.dataset.id;
                document.getElementById('delete_patient_id').value = patientId;

                const deleteModalEl = document.getElementById('delete-patient-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>

{{-- @include('pages.patients.modals.info')
@include('pages.patients.modals.edit')
@include('pages.patients.modals.delete') --}}

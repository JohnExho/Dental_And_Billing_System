<div class="card-body p-0">
    @if ($patients->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No patient found. Add one using the
            button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-striped table-primary">
                <thead class="bg-info">
                    <tr>
                        <th>Profile Pic</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                        @php
                            // Decide which image to show
                            $defaultProfile = match ($patient->sex) {
                                'male' => asset('storage/defaults/male.png'),
                                'female' => asset('storage/defaults/female.png'),
                                default => asset('storage/defaults/other.png'),
                            };

                            $profileUrl = $patient->profile_picture
                                ? Storage::url($patient->profile_picture)
                                : $defaultProfile;
                        @endphp

                        <tr>
                            <td>
                                <img src="{{ $profileUrl }}" alt="{{ $patient->full_name ?? 'Profile' }}"
                                    class="rounded-circle object-fit-cover border-primary border border-2"
                                    style="width: 60px; height: 60px;">

                            </td>
                            <td>{{ $patient->full_name }}</td>
                            <td>{{ $patient->mobile_no ?? 'N/A' }}/<br>{{ $patient->contact_no ?? 'N/A' }}
                            </td>
                            <td>{{ $patient->email ?? 'N/A' }}</td>
                            <td>
                                {{ $patient->full_address }}
                            </td>

                            <td class="text-end">

                                <a href="#" class="btn btn-outline-dark btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#add-waitlist-modal">
                                  <i class="fa-solid fa-hourglass-start"></i>
                                </a>

                                <form action="{{ route('specific-patient') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </form>



                                {{-- ✅ Edit Button --}}
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
                                    data-barangay_id="{{ optional($patient->address->barangay)->barangay_id }}"
                                    data-barangay_name="{{ optional($patient->address->barangay)->name }}"
                                    data-profile_picture="{{ $patient->profile_picture ? asset('storage/' . $patient->profile_picture) : '' }}"
                                    data-weight="{{ $patient->weight }}" data-height="{{ $patient->height }}"
                                    data-school="{{ $patient->school }}">
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


@if (!empty($patient))
    @include('pages.waitlist.modals.add')
    @include('pages.patients.modals.edit')
    @include('pages.patients.modals.delete')
@endif

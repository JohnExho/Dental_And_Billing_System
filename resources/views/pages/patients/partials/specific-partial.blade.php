    <div class="card-body">
        @if (!$patient)
            <p class="p-3 mb-0 text-danger text-center fs-5">Patient not found.</p>
        @else
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
            {{-- 🔹 Profile Header --}}
            <div
                class="d-flex align-items-center justify-content-between p-3 bg-primary bg-opacity-25 rounded-3 mb-4 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset( $profileUrl) }}" alt="Profile Picture" class="rounded-circle border border-2 border-primary"
                        style="width: 90px; height: 90px; object-fit: cover;">
                    <div>
                        <h4 class="mb-1 fw-semibold text-dark">{{ $patient->full_name }}</h4>
                        <p class="mb-0 text-muted small">
                            {{ ucfirst($patient->sex) }} • {{ $patient->civil_status ?? 'N/A' }}
                        </p>
                        <p class="mb-0 text-muted small">
                            {{ $patient->email ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#edit-patient-modal" data-id="{{ $patient->patient_id }}"
                        data-name="{{ $patient->full_name }}"
                        data-contact="{{ $patient->mobile_no }} | {{ $patient->contact_no }}"
                        data-email="{{ $patient->email }}" data-date_of_birth="{{ $patient->date_of_birth }}"
                        data-sex="{{ $patient->sex }}" data-civil_status="{{ $patient->civil_status }}"
                        data-occupation="{{ $patient->occupation }}" data-company="{{ $patient->company }}"
                        data-referral="{{ $patient->referral }}"
                        data-house_no="{{ optional($patient->address)->house_no }}"
                        data-street="{{ optional($patient->address)->street }}"
                        data-province_id="{{ optional($patient->address->province)->province_id }}"
                        data-province_name="{{ optional($patient->address->province)->name }}"
                        data-city_id="{{ optional($patient->address->city)->city_id }}"
                        data-city_name="{{ optional($patient->address->city)->name }}"
                        data-barangay_id="{{ optional($patient->address)->barangay_id }}"
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
                </div>
            </div>

            {{-- 🔹 Info Sections --}}
            <div class="row g-3">
                {{-- Personal Info --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-person-badge me-1"></i> Personal Information
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>First Name:</strong> {{ $patient->first_name ?? 'N/A' }}</li>
                                <li><strong>Middle Name:</strong> {{ $patient->middle_name ?? 'N/A' }}</li>
                                <li><strong>Last Name:</strong> {{ $patient->last_name ?? 'N/A' }}</li>
                                <li><strong>Date of Birth:</strong> {{ $patient->date_of_birth ?? 'N/A' }}</li>
                                <li><strong>Sex:</strong> {{ ucfirst($patient->sex) ?? 'N/A' }}</li>
                                <li><strong>Civil Status:</strong> {{ $patient->civil_status ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-telephone me-1"></i> Contact Information
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Mobile:</strong> {{ $patient->mobile_no ?? 'N/A' }}</li>
                                <li><strong>Contact:</strong> {{ $patient->contact_no ?? 'N/A' }}</li>
                                <li><strong>Email:</strong> {{ $patient->email ?? 'N/A' }}</li>
                                <li><strong>Address:</strong> {{ $patient->full_address ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Employment/School Info --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-building me-1"></i> Employment / School
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Occupation:</strong> {{ $patient->occupation ?? 'N/A' }}</li>
                                <li><strong>Company:</strong> {{ $patient->company ?? 'N/A' }}</li>
                                <li><strong>School:</strong> {{ $patient->school ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Medical Info --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-heart-pulse me-1"></i> Medical Details
                            </h6>
                            <ul class="list-unstyled mb-0">
                                <li><strong>Weight:</strong> {{ $patient->weight ? $patient->weight . ' kg' : 'N/A' }}</li>
                                <li><strong>Height:</strong> {{ $patient->height ? $patient->height . ' cm' : 'N/A' }}</li>
                                <li><strong>Referral:</strong> {{ $patient->referral ?? 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @include('pages.patients.modals.edit')
    @include('pages.patients.modals.delete')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-patient-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    document.getElementById('delete_patient_id').value = id;
                    new bootstrap.Modal(document.getElementById('delete-patient-modal')).show();
                });
            });
        });
    </script>

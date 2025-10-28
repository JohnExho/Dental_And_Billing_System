<!-- Add Waitlist Modal -->
<div class="modal fade" id="add-waitlist-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <form action="{{ route('process-create-waitlist') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i> Add New Waitlist
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <h6 class="text-muted mb-2"><i class="bi bi-building me-1"></i> Clinic</h6>
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control"
                                    value="{{ \App\Models\Clinic::find(session('clinic_id'))->name }}" disabled>
                                <input type="hidden" name="clinic_id" value="{{ session('clinic_id') }}">
                            </div>

                            <!-- Patient -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i> Patient
                                </label>
                                @if (!empty($patient))
                                    <div class="col-md-12 mb-3">
                                        <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">
                                        <input type="text" class="form-control" value="{{ $patient->full_name }}"
                                            disabled>
                                    </div>
                                @else
                                    <select id="provider-patient" class="form-select" name="patient_id">
                                        <option value="">-- Select Patient --</option>
                                        @if (!$patients->isEmpty())
                                            @foreach ($patients as $patient)
                                                <option value="{{ $patient->patient_id }}">{{ $patient->full_name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="add_new" class="bg-primary text-white"> Add Patient</option>
                                        @endif
                                    </select>
                                @endif
                            </div>

                            <!-- Associate -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-person-workspace me-1"></i> Associate
                                </label>
                                <select id="provider-associate" name="associate_id" class="form-select">
                                    <option value="">-- Select Associate --</option>
                                    @forelse ($associates as $associate)
                                        <option value="{{ $associate->associate_id }}">
                                            {{ trim($associate->full_name) !== '' ? $associate->full_name : 'N/A' }}
                                        </option>
                                    @empty
                                        <option value="" disabled>No Available Associate</option>
                                    @endforelse
                                </select>

                            </div>

                            <!-- Laboratory -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-building-gear me-1"></i> Laboratory
                                </label>
                                <select id="provider-laboratory" class="form-select" name="laboratory_id">
                                    <option value="">-- Select Laboratory --</option>
                                    @foreach ($laboratories as $laboratory)
                                        <option value="{{ $laboratory->laboratory_id }}">{{ $laboratory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check me-1"></i>Status
                                </label>
                                <select name="status" id="queue-status" class="form-select" disabled>
                                    <option value="waiting">Waiting</option>
                                    <option value="in_consultation">In Consultation</option>
                                    <option value="completed">Completed</option>
                                </select>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Waitlist
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('pages.patients.modals.add')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('add-waitlist-modal');
        if (!modal) return;

        const form = modal.querySelector('form');
        if (!form) return;

        // --- Phone & Email validation ---
        const phoneInputs = modal.querySelectorAll('.phone-number');

        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                this.value = value;
            });
        });

        form.addEventListener('submit', function(e) {
            const emailInput = form.querySelector('input[name="email"]');

            let phoneValid = true;
            phoneInputs.forEach(input => {
                if (input.value.length !== 11) phoneValid = false;
            });

            const emailPattern = /^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
            const emailValid = emailPattern.test(emailInput.value);

            if (!phoneValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    position: 'top-end',
                    timer: 3000,
                    text: 'Phone numbers must be exactly 11 digits.',
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
                return;
            }

            if (!emailValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    position: 'top-end',
                    timer: 3000,
                    text: 'Email must start with a letter and be valid.',
                    toast: true,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
                return;
            }
        });

        const patientSelect = document.getElementById('provider-patient');
        if (patientSelect) {
            patientSelect.addEventListener('change', function() {
                if (this.value === 'add_new') {
                    const addPatientModal = new bootstrap.Modal(document.getElementById(
                        'add-patient-modal'));

                    // Hide Waitlist modal
                    const waitlistModal = bootstrap.Modal.getInstance(modal);
                    if (waitlistModal) {
                        waitlistModal.hide();
                    }

                    // Show Add Patient modal
                    addPatientModal.show();

                    // Reset select value
                    this.value = "";
                }
            });
        }
    });
</script>

<!-- Add Waitlist Modal -->
<div class="modal fade" id="add-patient-waitlist-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <form action="{{--  --}}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i> Add New Waitlist
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Hidden IDs -->
                        <input type="hidden" name="clinic_id" value="{{ session('clinic_id') }}">
                        <input type="hidden" id="patient_id" name="patient_id">
                        <input type="hidden" id="associate_id" name="associate_id">
                        <input type="hidden" id="laboratory_id" name="laboratory_id">

                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2"><i class="bi bi-person me-1"></i> Patient Information</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <input type="text" class="form-control" name="first_name"
                                        placeholder="First Name" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="text" class="form-control" name="middle_name"
                                        placeholder="Middle Name">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <input type="text" class="form-control" name="last_name" placeholder="Last Name"
                                        required>
                                </div>
                            </div>

                            <h6 class="text-muted mt-4 mb-2"><i class="bi bi-envelope me-1"></i> Contact Details</h6>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                                    <input type="email" class="form-control" name="email"
                                        placeholder="Email address" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" class="form-control phone-number" name="contact_no"
                                            placeholder="Phone / Landline" maxlength="11" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                        <input type="text" class="form-control phone-number" name="mobile_no"
                                            placeholder="Mobile No" maxlength="11" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2"><i class="bi bi-building me-1"></i> Clinic</h6>
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control"
                                    value="{{ \App\Models\Clinic::find(session('clinic_id'))->name }}" disabled>
                            </div>

                            <!-- Provider-Linked Dropdowns -->
                            <h6 class="text-muted mt-4 mb-2"><i class="bi bi-person-badge me-1"></i> Provider
                                Assignments</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                  <input type="text" class="form-control"
                                    value="{{ $patient->full_name }}" disabled>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <select id="provider-associate" class="form-select">
                                        <option value="">-- Select Associate --</option>
                                        @foreach ($associates as $associate)
                                            <option value="{{ $associate->associate_id }}">{{ $associate->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <select id="provider-laboratory" class="form-select">
                                        <option value="">-- Select Laboratory --</option>
                                        @foreach ($laboratories as $laboratory)
                                            <option value="{{ $laboratory->laboratory_id }}">{{ $laboratory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the modal and form elements safely
        const modal = document.getElementById('add-waitlist-modal');
        if (!modal) return;

        const form = modal.querySelector('form');
        if (!form) return;

        // --- Phone & Email validation ---
        const phoneInputs = modal.querySelectorAll('.phone-number');

        // Restrict phone fields to 11 digits, numbers only
        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                this.value = value;
            });
        });

        // Handle form submission validation
        form.addEventListener('submit', function(e) {
            const emailInput = form.querySelector('input[name="email"]');

            let phoneValid = true;
            phoneInputs.forEach(input => {
                if (input.value.length !== 11) phoneValid = false;
            });

            const emailPattern = /^[A-Za-z][A-Za-z0-9._%+-]*@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
            const emailValid = emailPattern.test(emailInput.value);

            // Stop submission and show SweetAlert if invalid
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
    });
</script>

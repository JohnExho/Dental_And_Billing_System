<!-- Add Appointment Modal -->
<div class="modal fade" id="add-appointment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{route('process-create-appointment')}}" method="POST">
                @csrf

                <div class="modal-header bg-gradient bg-primary text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-calendar-check me-2"></i> Add Appointment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-0" style="min-height: 60vh;">
                        <!-- Left: Patient List -->
                        <div class="col-md-5 border-end p-3 overflow-auto" style="max-height: 60vh;">
                            <h6 class="fw-semibold">Patients</h6>
                            <ul class="list-group" id="patient_list">
                                @foreach($patients as $patient)
                                    <li class="list-group-item list-group-item-action" 
                                        data-id="{{ $patient->patient_id }}">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Right: Appointment Form -->
                        <div class="col-md-7 p-3">
                            <input type="hidden" name="patient_id" id="appointment_patient_id">

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label fw-semibold">Appointment Date</label>
                                <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" required
                                       value="{{ now()->format('Y-m-d\TH:i') }}" 
                                       min="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>

                            <div class="mb-3">
                                <label for="associate_id" class="form-label fw-semibold">Assign to Associate</label>
                                <select class="form-select" id="associate_id" name="associate_id" required>
                                    <option value="" disabled selected>Select an associate</option>
                                    @foreach ($associates as $associate)
                                        <option value="{{ $associate->associate_id }}">{{ $associate->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS to select patient from list -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const patientItems = document.querySelectorAll('#patient_list .list-group-item');
    const hiddenInput = document.getElementById('appointment_patient_id');

    patientItems.forEach(item => {
        item.addEventListener('click', () => {
            // Highlight selected patient
            patientItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            // Store selected patient id
            hiddenInput.value = item.dataset.id;
        });
    });
});
</script>

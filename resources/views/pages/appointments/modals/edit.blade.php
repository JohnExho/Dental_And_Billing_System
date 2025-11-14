<!-- Edit Appointment Modal -->
<div class="modal fade" id="edit-appointment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <form action="{{ route('process-update-appointment') }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="appointment_id" id="edit_appointment_id">

                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-pencil-square me-2"></i> Edit Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No Show</option>
                                <option value="rescheduled">Rescheduled</option>
                            </select>
                        </div>

                        <!-- Appointment Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Appointment Date</label>
                            <input type="datetime-local" class="form-control" id="edit_appointment_date"
                                   name="appointment_date" required>
                        </div>

                        <!-- Associate -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Assign to Associate</label>
                            <select class="form-select" id="edit_associate_id" name="associate_id">
                                <option value="" selected>No Associate</option>
                                @foreach ($associates as $associate)
                                    <option value="{{ $associate->associate_id }}">
                                        {{ $associate->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Close
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function openEditAppointmentModal(appointmentId, status, associateId, appointmentDate) {

    document.getElementById('edit_appointment_id').value = appointmentId;

    const statusSelect = document.getElementById('edit_status');
    const assocSelect = document.getElementById('edit_associate_id');
    const dateInput = document.getElementById('edit_appointment_date');

    // Set values
    if (statusSelect) statusSelect.value = status || 'scheduled';
    if (assocSelect) assocSelect.value = associateId || '';
    if (dateInput && appointmentDate) dateInput.value = appointmentDate.replace(' ', 'T');

    // Show modal
    new bootstrap.Modal(document.getElementById('edit-appointment-modal')).show();
}

</script>
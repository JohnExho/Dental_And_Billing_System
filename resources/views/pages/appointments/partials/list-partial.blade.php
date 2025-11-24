<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-calendar-check me-2"></i> appointments
        </h6>
    </div>

    <div class="card-body p-0 d-flex flex-column">
        @if ($appointments->isEmpty())
            <div class="table-responsive flex-grow-1" style="overflow-y: auto;">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Author</th>
                            <th>Patient</th>
                            <th>Appointment Date</th>
                            <th>Associate</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->account?->full_name ?? 'Unknown' }}</td>
                                <td>{{ $appointment->patient?->full_name ?? 'Unknown' }}</td>
                                <td>{{ $appointment->appointment_date?->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $appointment->associate?->full_name ?? '-' }}</td>
                                <td>{{ ucfirst($appointment->status) ?? '-' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openEditAppointmentModal(
    {{ json_encode($appointment->appointment_id) }},
    {{ json_encode($appointment->status ?? '') }},
    {{ json_encode($appointment->associate_id ?? '') }},
    {{ json_encode($appointment->appointment_date?->format('Y-m-d\TH:i') ?? '') }}
)">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm delete-appointment-btn"
                                        data-id="{{ $appointment->appointment_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-2 px-3">
                {{ $appointments->links('vendor.pagination.bootstrap-5') }}
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No appointments available.
            </p>
        @endif
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-appointment-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const appointmentId = this.dataset.id;
                const input = document.getElementById('delete_appointment_id');
                if (input) input.value = appointmentId;

                const deleteModalEl = document.getElementById('delete-appointment-modal');
                if (deleteModalEl) new bootstrap.Modal(deleteModalEl).show();
            });
        });
    });

    function _setSelectValueCaseInsensitive(selectEl, value) {
        if (!selectEl || value === null || value === undefined) return;
        const v = String(value);
        for (let i = 0; i < selectEl.options.length; i++) {
            if (String(selectEl.options[i].value).toLowerCase() === v.toLowerCase()) {
                selectEl.selectedIndex = i;
                return;
            }
        }
    }

    function openEditAppointmentModal(appointmentId, status, associateId, appointmentDate) {
        document.getElementById('edit_appointment_id').value = appointmentId;
        document.getElementById('edit_associate_id').value = associateId;

        const statusSelect = document.querySelector('#edit-appointment-modal #edit_status');
        _setSelectValueCaseInsensitive(statusSelect, status);

        // Populate date/time input
        const dateInput = document.getElementById('edit_appointment_date');
        if (dateInput && appointmentDate) dateInput.value = appointmentDate;

        new bootstrap.Modal(document.getElementById('edit-appointment-modal')).show();
    }
</script>

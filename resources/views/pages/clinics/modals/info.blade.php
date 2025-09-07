<!-- Clinic Detail Modal -->
<div class="modal fade" id="clinic-detail-modal" tabindex="-1" aria-labelledby="clinicDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="clinicDetailLabel">Clinic Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <h4 id="clinic-name" class="fw-bold text-primary mb-3"></h4>
                <div class="row g-4">

                    <!-- Left Column -->
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-card-text me-2 text-secondary"></i>
                                <strong>Description:</strong> 
                                <span id="clinic-description" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-geo-alt me-2 text-secondary"></i>
                                <strong>Address:</strong> 
                                <span id="clinic-address" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-envelope me-2 text-secondary"></i>
                                <strong>Email:</strong> 
                                <span id="clinic-email" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-telephone me-2 text-secondary"></i>
                                <strong>Contact:</strong>
                                <span id="clinic-contact" class="text-muted">
                                    <i class="bi bi-telephone-fill me-1"></i><span id="clinic-contact-no"></span><br>
                                    <i class="bi bi-phone-fill me-1"></i><span id="clinic-mobile-no"></span>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <p class="fw-semibold mb-2">Schedule Summary</p>
                        <p id="clinic-schedule" class="badge bg-light text-dark border mb-3"></p>

                        <p class="fw-semibold mb-2">Full Schedule</p>
                        <div class="table-responsive">
                            <table id="clinic-schedule-details" class="table table-sm table-striped table-bordered mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Day</th>
                                        <th class="text-center">Time</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function formatTime(timeStr) {
        if (!timeStr) return '';
        const [hourStr, minuteStr] = timeStr.split(':');
        let hour = parseInt(hourStr, 10);
        const minute = minuteStr.padStart(2, '0');
        const ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12;
        return `${hour}:${minute} ${ampm}`;
    }

    const clinicModal = document.getElementById('clinic-detail-modal');

    clinicModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const data = {
            name: button.getAttribute('data-name'),
            description: button.getAttribute('data-description'),
            email: button.getAttribute('data-email'),
            contact: button.getAttribute('data-contact'),
            address: button.getAttribute('data-address'),
            schedule: button.getAttribute('data-schedule'),
            schedules: JSON.parse(button.getAttribute('data-schedules') || '[]')
        };

        const [contactNo, mobileNo] = (data.contact || '').split(' / ');

        // Update left column
        clinicModal.querySelector('#clinic-name').textContent = data.name;
        clinicModal.querySelector('#clinic-description').textContent = data.description || 'No description provided';
        clinicModal.querySelector('#clinic-email').textContent = data.email || 'N/A';
        clinicModal.querySelector('#clinic-contact-no').textContent = contactNo || 'N/A';
        clinicModal.querySelector('#clinic-mobile-no').textContent = mobileNo || 'N/A';
        clinicModal.querySelector('#clinic-address').textContent = data.address || 'N/A';
        clinicModal.querySelector('#clinic-schedule').textContent = data.schedule || 'N/A';

        // Build weekly schedule table
        const tbody = clinicModal.querySelector('#clinic-schedule-details tbody');
        tbody.innerHTML = '';
        const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        weekDays.forEach(day => {
            const sched = data.schedules.find(s => s.day_of_week === day);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-center fw-semibold">${day}</td>
                <td class="text-center">${sched ? `${formatTime(sched.start_time)} - ${formatTime(sched.end_time)}` : 'N/A'}</td>
            `;
            tbody.appendChild(row);
        });
    });
</script>

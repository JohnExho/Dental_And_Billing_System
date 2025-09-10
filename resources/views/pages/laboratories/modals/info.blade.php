<!-- laboratory Detail Modal -->
<div class="modal fade" id="laboratory-detail-modal" tabindex="-1" aria-labelledby="laboratoryDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="laboratoryDetailLabel">laboratory Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <h4 id="laboratory-name" class="fw-bold text-primary mb-3"></h4>
                <div class="row g-4">

                    <!-- Left Column -->
                    <div class="col-md-12">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-card-text me-2 text-secondary"></i>
                                <strong>Description:</strong>
                                <span id="laboratory-description" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-geo-alt me-2 text-secondary"></i>
                                <strong>Speciality:</strong>
                                <span id="laboratory-speciality" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-geo-alt me-2 text-secondary"></i>
                                <strong>Address:</strong>
                                <span id="laboratory-address" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-envelope me-2 text-secondary"></i>
                                <strong>Email:</strong>
                                <span id="laboratory-email" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-telephone me-2 text-secondary"></i>
                                <strong>Contact:</strong>
                                <span id="laboratory-contact" class="text-muted">
                                    <i class="bi bi-telephone-fill me-1"></i><span
                                        id="laboratory-contact-no"></span><br>
                                    <i class="bi bi-phone-fill me-1"></i><span id="laboratory-mobile-no"></span>
                                </span>
                            </li>
                        </ul>
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

    const laboratoryModal = document.getElementById('laboratory-detail-modal');

    laboratoryModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const data = {
            name: button.getAttribute('data-name'),
            description: button.getAttribute('data-description'),
            email: button.getAttribute('data-email'),
            contact: button.getAttribute('data-contact'),
            address: button.getAttribute('data-address'),
            speciality: button.getAttribute('data-speciality')
        };

        const [contactNo, mobileNo] = (data.contact || '').split(' / ');

        // Update left column
        laboratoryModal.querySelector('#laboratory-name').textContent = data.name;
        laboratoryModal.querySelector('#laboratory-description').textContent = data.description ||
            'No description provided';
        laboratoryModal.querySelector('#laboratory-email').textContent = data.email || 'N/A';
        laboratoryModal.querySelector('#laboratory-contact-no').textContent = contactNo || 'N/A';
        laboratoryModal.querySelector('#laboratory-mobile-no').textContent = mobileNo || 'N/A';
        laboratoryModal.querySelector('#laboratory-address').textContent = data.address || 'N/A';
        laboratoryModal.querySelector('#laboratory-speciality').textContent = data.speciality || 'N/A';
    });
</script>

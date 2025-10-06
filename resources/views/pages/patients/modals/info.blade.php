<!-- Patient Detail Modal -->
<div class="modal fade" id="patient-detail-modal" tabindex="-1" aria-labelledby="patientDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm border-0 rounded-3">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="patientDetailLabel">Patient Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Profile Section -->
                <div class="d-flex align-items-center mb-4">
                    <img id="patient-profile" src="https://placehold.co/80x80" 
                        class="rounded-circle border shadow-sm me-3" style="width: 70px; height: 70px; object-fit: cover;">
                    <div>
                        <h4 id="patient-name" class="fw-bold text-primary mb-0"></h4>
                        <small id="patient-email" class="text-muted"></small>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="row g-3">

                    <div class="col-md-6">
                        <strong><i class="bi bi-telephone me-1"></i> Contact:</strong>
                        <div id="patient-contact-no" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-geo-alt me-1"></i> Address:</strong>
                        <div id="patient-address" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-gender-ambiguous me-1"></i> Sex:</strong>
                        <div id="patient-sex" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-calendar-event me-1"></i> Date of Birth:</strong>
                        <div id="patient-dob" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-heart me-1"></i> Civil Status:</strong>
                        <div id="patient-civil" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-briefcase me-1"></i> Occupation:</strong>
                        <div id="patient-occupation" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-building me-1"></i> Company:</strong>
                        <div id="patient-company" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-bullseye me-1"></i> School:</strong>
                        <div id="patient-school" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-speedometer2 me-1"></i> Weight:</strong>
                        <div id="patient-weight" class="text-muted"></div>
                    </div>

                    <div class="col-md-6">
                        <strong><i class="bi bi-arrows-expand me-1"></i> Height:</strong>
                        <div id="patient-height" class="text-muted"></div>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const patientModal = document.getElementById('patient-detail-modal');

    patientModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const data = {
            name: button.getAttribute('data-name'),
            email: button.getAttribute('data-email'),
            contact: button.getAttribute('data-contact'),
            address: button.getAttribute('data-address'),
            sex: button.getAttribute('data-sex'),
            dob: button.getAttribute('data-date_of_birth'),
            civil_status: button.getAttribute('data-civil_status'),
            occupation: button.getAttribute('data-occupation'),
            company: button.getAttribute('data-company'),
            weight: button.getAttribute('data-weight'),
            height: button.getAttribute('data-height'),
            school: button.getAttribute('data-school'),
            profile_picture: button.getAttribute('data-profile_picture'),
        };

        const [mobileNo, contactNo] = (data.contact || '').split('|').map(item => item.trim());

        // Update modal fields
        patientModal.querySelector('#patient-name').textContent = data.name || 'Unnamed patient';
        patientModal.querySelector('#patient-email').textContent = data.email || 'N/A';
        patientModal.querySelector('#patient-contact-no').textContent = mobileNo || contactNo || 'N/A';
        patientModal.querySelector('#patient-address').textContent = data.address || 'N/A';
        patientModal.querySelector('#patient-sex').textContent = data.sex || 'N/A';
        patientModal.querySelector('#patient-dob').textContent = data.dob || 'N/A';
        patientModal.querySelector('#patient-civil').textContent = data.civil_status || 'N/A';
        patientModal.querySelector('#patient-occupation').textContent = data.occupation || 'N/A';
        patientModal.querySelector('#patient-company').textContent = data.company || 'N/A';
        patientModal.querySelector('#patient-weight').textContent = data.weight ? data.weight + ' kg' : 'N/A';
        patientModal.querySelector('#patient-height').textContent = data.height ? data.height + ' cm' : 'N/A';
        patientModal.querySelector('#patient-school').textContent = data.school || 'N/A';
        patientModal.querySelector('#patient-profile').src = data.profile_picture || 'https://placehold.co/80x80';
    });
</script>

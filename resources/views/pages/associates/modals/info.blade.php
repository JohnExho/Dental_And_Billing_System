<!-- associate Detail Modal -->
<div class="modal fade" id="associate-detail-modal" tabindex="-1" aria-labelledby="associateDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="associateDetailLabel">associate Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <h4 id="associate-name" class="fw-bold text-primary mb-3"></h4>
                <div class="row g-4">

                    <!-- associate Info -->
                    <div class="col-md-12">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-geo-alt me-2 text-secondary"></i>
                                <strong>Address:</strong> 
                                <span id="associate-address" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-envelope me-2 text-secondary"></i>
                                <strong>Email:</strong> 
                                <span id="associate-email" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-heart-pulse me-2 text-secondary"></i>
                                <strong>Speciality:</strong> 
                                <span id="associate-speciality" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-telephone me-2 text-secondary"></i>
                                <strong>Contact:</strong>
                                <span class="text-muted">
                                    <i class="bi bi-telephone-fill me-1"></i><span id="associate-contact-no"></span><br>
                                    <i class="bi bi-phone-fill me-1"></i><span id="associate-mobile-no"></span>
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
    const associateModal = document.getElementById('associate-detail-modal');

    associateModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const data = {
            first_name: button.getAttribute('data-first-name'),
            middle_name: button.getAttribute('data-middle-name'),
            last_name: button.getAttribute('data-last-name'),
            email: button.getAttribute('data-email'),
            contact: button.getAttribute('data-contact'),
            address: button.getAttribute('data-address'),
            speciality: button.getAttribute('data-speciality')
        };

        const [contactNo, mobileNo] = (data.contact || '').split(' / ');

        // Combine names into one string
        const fullName = [data.first_name, data.middle_name, data.last_name]
            .filter(Boolean) // remove null/empty values
            .join(' ');

        // Update modal fields
        associateModal.querySelector('#associate-name').textContent = fullName || 'Unnamed associate';
       
        associateModal.querySelector('#associate-email').textContent = data.email || 'N/A';
        associateModal.querySelector('#associate-contact-no').textContent = contactNo || 'N/A';
        associateModal.querySelector('#associate-mobile-no').textContent = mobileNo || 'N/A';
        associateModal.querySelector('#associate-address').textContent = data.address || 'N/A';
        associateModal.querySelector('#associate-speciality').textContent = data.speciality || 'N/A' ;
    });
</script>

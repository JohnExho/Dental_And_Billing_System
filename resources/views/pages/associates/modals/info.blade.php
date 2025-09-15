{{-- <!-- staff Detail Modal -->
<div class="modal fade" id="staff-detail-modal" tabindex="-1" aria-labelledby="staffDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="staffDetailLabel">Staff Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <h4 id="staff-name" class="fw-bold text-primary mb-3"></h4>
                <div class="row g-4">

                    <!-- Staff Info -->
                    <div class="col-md-12">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-geo-alt me-2 text-secondary"></i>
                                <strong>Address:</strong> 
                                <span id="staff-address" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-envelope me-2 text-secondary"></i>
                                <strong>Email:</strong> 
                                <span id="staff-email" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-telephone me-2 text-secondary"></i>
                                <strong>Contact:</strong>
                                <span class="text-muted">
                                    <i class="bi bi-telephone-fill me-1"></i><span id="staff-contact-no"></span><br>
                                    <i class="bi bi-phone-fill me-1"></i><span id="staff-mobile-no"></span>
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
    const staffModal = document.getElementById('staff-detail-modal');

    staffModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const data = {
            first_name: button.getAttribute('data-first-name'),
            middle_name: button.getAttribute('data-middle-name'),
            last_name: button.getAttribute('data-last-name'),
            email: button.getAttribute('data-email'),
            contact: button.getAttribute('data-contact'),
            address: button.getAttribute('data-address')
        };

        const [contactNo, mobileNo] = (data.contact || '').split(' / ');

        // Combine names into one string
        const fullName = [data.first_name, data.middle_name, data.last_name]
            .filter(Boolean) // remove null/empty values
            .join(' ');

        // Update modal fields
        staffModal.querySelector('#staff-name').textContent = fullName || 'Unnamed Staff';
       
        staffModal.querySelector('#staff-email').textContent = data.email || 'N/A';
        staffModal.querySelector('#staff-contact-no').textContent = contactNo || 'N/A';
        staffModal.querySelector('#staff-mobile-no').textContent = mobileNo || 'N/A';
        staffModal.querySelector('#staff-address').textContent = data.address || 'N/A';
    });
</script> --}}

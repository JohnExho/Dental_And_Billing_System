<!-- service Detail Modal -->
<div class="modal fade" id="service-detail-modal" tabindex="-1" aria-labelledby="serviceDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="serviceDetailLabel">Service Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <h4 id="service-name" class="fw-bold text-primary mb-3"></h4>
                <div class="row g-4">
                    <!-- service Info -->
                    <div class="col-md-12">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Description:</strong>
                                <span id="service-description" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <strong>Type:</strong>
                                <span id="service-type" class="text-muted"></span>
                            </li>
                            <li class="list-group-item">
                                <strong>Default Price:</strong>
                                <span id="service-default-price" class="text-muted"></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <h6 class="fw-bold mt-4">Clinic Prices</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Clinic</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="service-clinics-list">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
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
    const serviceModal = document.getElementById('service-detail-modal');

    serviceModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        // Retrieve the data attributes passed from the button
        const data = {
            name: button.getAttribute('data-name'),
            description: button.getAttribute('data-description'),
            serviceType: button.getAttribute('data-service_type'),
            defaultPrice: button.getAttribute('data-default_price'),
            clinics: JSON.parse(button.getAttribute('data-clinics') || '[]'),
        };

        // Update modal fields with service data
        serviceModal.querySelector('#service-name').textContent = data.name || 'Unnamed service';
        serviceModal.querySelector('#service-description').textContent = data.description || 'No description available';
        serviceModal.querySelector('#service-type').textContent = data.serviceType || 'N/A';
        serviceModal.querySelector('#service-default-price').textContent = data.defaultPrice || 'N/A';

        // Populate clinic prices table
        const clinicTable = serviceModal.querySelector('#service-clinics-list');
        clinicTable.innerHTML = ''; // Clear any previous entries

        if (data.clinics.length > 0) {
            data.clinics.forEach(clinic => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${clinic.name}</td>
                    <td>₱${clinic.price}</td>
                `;
                clinicTable.appendChild(row);
            });
        } else {
            clinicTable.innerHTML = `<tr><td colspan="2" class="text-center text-muted">No clinic prices available</td></tr>`;
        }
    });
</script>

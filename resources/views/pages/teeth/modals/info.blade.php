<!-- tooth Detail Modal -->
<div class="modal fade" id="tooth-detail-modal" tabindex="-1" aria-labelledby="toothDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="toothDetailLabel">Tooth Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <h4 id="tooth-name" class="fw-bold text-primary mb-3"></h4>
                <div class="row g-4">
                    <!-- tooth Info -->
                    <div class="col-md-12">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                 <i class="fa-solid fa-tooth"></i>
                                <strong>Number:</strong>
                                <span id="tooth-number" class="text-muted"></span>
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
                        <tbody id="tooth-clinics"></tbody>
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
    const toothModal = document.getElementById('tooth-detail-modal');

    toothModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const data = {
            name: button.getAttribute('data-name'),
            number: button.getAttribute('data-number'),
            clinics: JSON.parse(button.getAttribute('data-clinics') || '[]')
        };

        // Update modal fields
        toothModal.querySelector('#tooth-name').textContent = data.name || 'Unnamed tooth';
        toothModal.querySelector('#tooth-number').textContent = data.number || 'N/A';

        // Update clinic prices table
        const clinicTable = toothModal.querySelector('#tooth-clinics');
        clinicTable.innerHTML = '';

        if (data.clinics.length > 0) {
            data.clinics.forEach(c => {
                clinicTable.innerHTML += `
                    <tr>
                        <td>${c.name}</td>
                        <td>₱${c.price}</td>
                    </tr>`;
            });
        } else {
            clinicTable.innerHTML = `<tr><td colspan="2" class="text-center text-muted">No clinic prices available</td></tr>`;
        }
    });
</script>

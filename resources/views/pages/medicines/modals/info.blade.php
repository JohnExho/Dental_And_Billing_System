<!-- Medicine Detail Modal -->
<div class="modal fade" id="medicine-detail-modal" tabindex="-1" aria-labelledby="medicineDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="medicineDetailLabel">Medicine Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <h4 id="medicine-name" class="fw-bold text-primary mb-3"></h4>
                <p id="medicine-description" class="text-muted"></p>

                <div class="mb-3">
                    <strong>Total Stock:</strong>
                    <span id="medicine-stock" class="px-3 py-2 rounded-pill"></span>
                </div>

                <div class="mb-3">
                    <strong>Default Price:</strong>
                    <span id="medicine-default-price" class="px-2 py-1 rounded-pill"></span>
                </div>


                <h6 class="fw-bold mt-4">Clinic Availability</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Clinic</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody id="medicine-clinics"></tbody>
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
    const medicineModal = document.getElementById('medicine-detail-modal');

    medicineModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const data = {
            name: button.getAttribute('data-name'),
            description: button.getAttribute('data-description'),
            stock: button.getAttribute('data-stock'),
            clinics: JSON.parse(button.getAttribute('data-clinics') || '[]'),
            default_price: button.getAttribute('data-default_price') || '—',

        };

        medicineModal.querySelector('#medicine-name').textContent = data.name || 'Unnamed medicine';
        medicineModal.querySelector('#medicine-description').textContent = data.description || 'No description';
        medicineModal.querySelector('#medicine-stock').textContent = data.stock ?? '0';
        medicineModal.querySelector('#medicine-default-price').textContent = `₱${data.default_price}`;


        const clinicTable = medicineModal.querySelector('#medicine-clinics');
        clinicTable.innerHTML = '';

        if (data.clinics.length > 0) {
            data.clinics.forEach(c => {
                clinicTable.innerHTML += `
                    <tr>
                        <td>${c.name}</td>
                        <td>₱${c.price}</td>
                        <td>${c.stock}</td>
                    </tr>`;
            });
        } else {
            clinicTable.innerHTML =
                `<tr><td colspan="3" class="text-center text-muted">No clinics available</td></tr>`;
        }
    });
</script>

<!-- Modal: Process Bill to Payment -->
<div class="modal fade" id="process-bill-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="process-bill-form" action="" method="POST">
                @csrf
                <input type="hidden" name="bill_id" id="process_bill_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title">Process Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- LEFT: Receipt preview -->
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold">Receipt</h6>
                            <div class="mb-2 text-muted small">Preview of items included in the bill</div>

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless" id="process-receipt-items">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-end">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Populated by JS -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <div class="small text-muted">Reference</div>
                                <div id="process-bill-ref" class="fw-bold">-</div>
                                <div class="small text-muted mt-2">Author</div>
                                <div id="process-bill-author" class="fw-bold">-</div>
                            </div>
                        </div>

                        <!-- RIGHT: Summary and payment inputs -->
                        <div class="col-md-6">
                            <h6 class="fw-bold">Summary</h6>

                            <div class="mb-2 small text-muted">Breakdown</div>

                            <div class="mb-3 d-flex justify-content-between">
                                <div>Services</div>
                                <div><strong id="process-service-price">0.00</strong></div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between">
                                <div>Tooth</div>
                                <div><strong id="process-tooth-price">0.00</strong></div>
                            </div>

                            <div class="mb-3">
                                <label for="process-discount" class="form-label">Discount</label>
                                <input type="number" step="0.01" min="0" value="0.00" class="form-control"
                                    id="process-discount" name="discount">
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="fs-6 fw-bold">Total Amount</div>
                                <div class="fs-5 fw-bold text-primary">PHP <span id="process-total-amount">0.00</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="process-payment-method" class="form-label">Payment Method</label>
                                <select class="form-select" id="process-payment-method" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="process-paid-at" class="form-label">Paid At</label>
                                <input type="datetime-local" id="process-paid-at" name="paid_at" class="form-control">
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary w-100" id="process-payment-btn">Process
                                    Payment</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Inject provider data into JS --}}
<script>
    const ServicesProvider = @json($services ?? []);
    const ToothProvider = @json($teeth ?? []);

    function fmt(value) {
        return parseFloat(value || 0).toFixed(2);
    }

    function recalcProcessTotals() {
        const service = parseFloat(document.getElementById('process-service-price').dataset.raw || 0);
        const tooth = parseFloat(document.getElementById('process-tooth-price').dataset.raw || 0);
        const discount = parseFloat(document.getElementById('process-discount').value || 0);
        const total = Math.max(0, service + tooth - discount);
        document.getElementById('process-total-amount').textContent = fmt(total);
    }

    function findServiceById(id) {
        return ServicesProvider.find(s => s.id == id);
    }

    function findToothById(id) {
        return ToothProvider.find(t => t.id == id);
    }

    // billServices and billTeeth should be arrays of IDs
    function openProcessBillModal(billId, ref, author, billServices = [], billTeeth = []) {
        const billInput = document.getElementById('process_bill_id');
        if (billInput) billInput.value = billId;

        const receiptTbody = document.querySelector('#process-receipt-items tbody');
        receiptTbody.innerHTML = '';

        document.getElementById('process-bill-ref').textContent = ref || '-';
        document.getElementById('process-bill-author').textContent = author || '-';
        document.getElementById('process-paid-at').value = new Date().toISOString().slice(0, 16);

        let serviceTotal = 0;
        let toothTotal = 0;

        // Add all services
        billServices.forEach(serviceId => {
            const s = findServiceById(serviceId);
            if (s) {
                serviceTotal += parseFloat(s.final_price || 0);
                const row = `<tr><td>${s.name}</td><td class="text-end">${fmt(s.final_price)}</td></tr>`;
                receiptTbody.insertAdjacentHTML('beforeend', row);
            }
        });

        // Add all teeth
        billTeeth.forEach(toothId => {
            const t = findToothById(toothId);
            if (t) {
                toothTotal += parseFloat(t.final_price || 0);
                const row = `<tr><td>${t.name}</td><td class="text-end">${fmt(t.final_price)}</td></tr>`;
                receiptTbody.insertAdjacentHTML('beforeend', row);
            }
        });

        // Update totals
        document.getElementById('process-service-price').textContent = fmt(serviceTotal);
        document.getElementById('process-service-price').dataset.raw = serviceTotal;
        document.getElementById('process-tooth-price').textContent = fmt(toothTotal);
        document.getElementById('process-tooth-price').dataset.raw = toothTotal;
        document.getElementById('process-discount').value = '0.00';

        recalcProcessTotals();

        const el = document.getElementById('process-bill-modal');
        const bsModal = new bootstrap.Modal(el);
        bsModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const discountEl = document.getElementById('process-discount');
        if (discountEl) discountEl.addEventListener('input', recalcProcessTotals);
    });
</script>

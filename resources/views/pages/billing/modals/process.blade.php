<!-- Modal: Process Bill to Payment -->

<style>
    #process-receipt-container {
        max-height: 400px;
        overflow-y: auto;
    }

    #process-receipt-items tbody tr.grouped-tooth {
        padding-left: 1rem;
        font-size: 0.9rem;
        color: #555;
    }

    #process-receipt-items tbody tr.group-header {
        cursor: pointer;
        background-color: #f8f9fa;
    }
</style>

<div class="modal fade" id="process-bill-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="process-bill-form" action="{{ route('process-process-bill') }}" method="POST">
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

                            <div class="table-responsive" id="process-receipt-container">
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

                            <div class="mt-3">
                                <div class="small text-muted" id="process-payment-method-info">Payment Method</div>
                                <div id="process-payment-method-display" class="fw-bold">-</div>

                                <div class="small text-muted mt-2" id="process-paid-at-info">Paid At</div>
                                <div id="process-paid-at-display" class="fw-bold">-</div>
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
                                <label for="process-discount" class="form-label">Discount (%)</label>
                                <input type="number" step="0.01" min="0" max="100" value="0"
                                    class="form-control" id="process-discount" name="discount"
                                    aria-describedby="discountHelp" onkeydown="return isNumericKey(event)">
                                <div id="discountHelp" class="form-text small text-muted">Enter percentage (0-100)</div>
                            </div>


                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="fs-6 fw-bold">Total Amount</div>
                                <div class="fs-5 fw-bold text-primary">PHP <span id="process-total-amount">0.00</span>
                                </div>
                                <input type="hidden" name="amount_paid" id="process-amount-paid" value="0.00">
                            </div>

                            <div class="mb-3">
                                <label for="process-payment-method" class="form-label">Payment Method</label>
                                <select class="form-select" id="process-payment-method" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>


                            <div class="mb-3" id="credit_card_type" style="display: none;">
                                <label for="credit_card_type" class="form-label">Credit Card Type</label>
                                <select class="form-select" id="credit_card_type" name="credit_card_type">
                                    <option value="visa">Visa</option>
                                    <option value="mastercard">MasterCard</option>
                                    <option value="amex">American Express</option>
                                </select>
                            </div>


                            <div class="mb-3" id="online-payment-options" style="display: none;">
                                <label for="online-payment-type" class="form-label">Online Payment Type</label>
                                <select class="form-select" id="online-payment-type" name="online_payment_type">
                                    <option value="gcash">GCash</option>
                                    <option value="maya">Maya</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3" id="other-payment-field" style="display: none;">
                                <label for="other-payment-details" class="form-label">Specify Payment Method</label>
                                <input type="text" class="form-control" id="other-payment-details"
                                    name="other_payment_details">
                            </div>

                            <div class="mb-3">
                                <label for="process-paid-at" class="form-label">Paid At</label>
                                <input type="datetime-local" id="process-paid-at" name="paid_at"
                                    class="form-control" readonly>
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



<script>
    document.getElementById('process-payment-method').addEventListener('change', function() {
        const onlineOptions = document.getElementById('online-payment-options');
        onlineOptions.style.display = this.value === 'online' ? 'block' : 'none';
    });

    document.getElementById('online-payment-type').addEventListener('change', function() {
        const otherField = document.getElementById('other-payment-field');
        otherField.style.display = this.value === 'other' ? 'block' : 'none';
    });

    document.getElementById('process-payment-method').addEventListener('change', function() {
        const creditCardType = document.getElementById('credit_card_type');
        creditCardType.style.display = this.value === 'credit_card' ? 'block' : 'none';
    });

    document.addEventListener('DOMContentLoaded', function() {
        const discountEl = document.getElementById('process-discount');
        if (discountEl) discountEl.addEventListener('input', recalcProcessTotals);
    });

    // Allow numeric input, one decimal point, and editing/navigation keys (Backspace/Delete/Arrows/etc.)
    function isNumericKey(e) {
        const navKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'];
        if (navKeys.includes(e.key)) return true;

        // Allow digits
        if (/[0-9]/.test(e.key)) return true;

        // Allow single decimal point
        if (e.key === '.') {
            try {
                return !e.target.value.includes('.');
            } catch (err) {
                return false;
            }
        }

        // Disallow exponential 'e' and everything else
        return false;
    }

    // Prevent paste so invalid pasted content can't bypass the key filter
    document.addEventListener('DOMContentLoaded', function() {
        const discountEl = document.getElementById('process-discount');
        if (discountEl) {
            discountEl.addEventListener('paste', function(e) {
                e.preventDefault();
            });
        }
    });
</script>
{{-- Inject provider data into JS --}}
<script>
    const ServicesProvider = @json($services ?? []);
    const ToothProvider = @json($teeth ?? []);
    const MedicineProvider = @json($medicines ?? []);
    const PrescriptionProvider = @json($prescriptions ?? []);

    function fmt(value) {
        return parseFloat(value || 0).toFixed(2);
    }

    function recalcProcessTotals() {
        const service = parseFloat(document.getElementById('process-service-price').dataset.raw || 0);
        const tooth = parseFloat(document.getElementById('process-tooth-price').dataset.raw || 0);
        // Discount is a percentage (0-100). Apply to subtotal.
        const discount = parseFloat(document.getElementById('process-discount').value || 0);
        const subtotal = service + tooth;
        const total = Math.max(0, subtotal * (1 - Math.min(Math.max(discount, 0), 100) / 100));
        document.getElementById('process-total-amount').textContent = fmt(total);
        document.getElementById('process-amount-paid').value = fmt(total);

    }

    function findServiceById(id) {
        return ServicesProvider.find(s => (s.id || s.service_id || s._id) == id);
    }

    function findToothById(id) {
        return ToothProvider.find(t => (t.id || t.tooth_list_id || t.toothListId || t.toothList_id) == id);
    }

    function findMedicineByPrescriptionId(id) {
        return MedicineProvider.find(
            m => (m.prescription_id || m.id || m._id) == id
        );
    }

    function findPrescriptionById(id) {
        return PrescriptionProvider.find(
            p => (p.prescription_id || p.id || p._id) == id
        );
    }


    function getManilaLocalDatetime() {
        const now = new Date();
        const options = {
            timeZone: 'Asia/Manila',
            hour12: false
        };

        // Get Manila components
        const manilaStr = now.toLocaleString('en-US', options); // e.g., "11/7/2025, 15:03:45"
        const [datePart, timePart] = manilaStr.split(', ');

        const [month, day, year] = datePart.split('/').map(Number);
        const [hour, minute] = timePart.split(':').map(Number);

        // Format for datetime-local
        const pad = n => n.toString().padStart(2, '0');
        return `${year}-${pad(month)}-${pad(day)}T${pad(hour)}:${pad(minute)}`;
    }


    // billItems can be either:
    // - an array of IDs (legacy behaviour, looked up via Providers), or
    // - an array of objects with { item_type, name, amount } (existing bill_items shape)
    // billTeeth remains supported for legacy cases.
    function openProcessBillModal(billId, ref, author, billItems = [], billTeeth = [], readOnly = false) {
        const summaryCol = document.querySelector('#process-bill-modal .col-md-6:last-child');

        if (readOnly) summaryCol.style.display = 'none';
        else summaryCol.style.display = 'block';
        if (readOnly) {
            const now = getManilaLocalDatetime();
            document.getElementById('process-paid-at').value = now;
            document.getElementById('process-paid-at-display').textContent = now;
            document.getElementById('process-payment-method-display').textContent =
                document.getElementById('process-payment-method').value;
        } else {
            document.getElementById('process-paid-at-info').style.display = 'none';
            document.getElementById('process-payment-method-info').style.display = 'none';
                        document.getElementById('process-paid-at-display').style.display = 'none';
            document.getElementById('process-payment-method-display').style.display = 'none';
        }

        const billInput = document.getElementById('process_bill_id');
        if (billInput) billInput.value = billId;

        const receiptTbody = document.querySelector('#process-receipt-items tbody');
        receiptTbody.innerHTML = '';

        document.getElementById('process-bill-ref').textContent = ref || '-';
        document.getElementById('process-bill-author').textContent = author || '-';
        // Then in your modal:
        document.getElementById('process-paid-at').value = getManilaLocalDatetime();


        let serviceTotal = 0;
        let toothTotal = 0;

        // billItems may be objects ({item_type, name, amount}) or IDs
        billItems.forEach(it => {
            // If it's an object with amount/name, render directly
            // ✅ If it's an object, detect medicine FIRST.
            if (it && typeof it === 'object') {

                if (it.item_type === 'prescription' || it.is_medicine === true) {
                    // Fix: Pass the ID directly, not a callback

                    const medicineItem = findMedicineByPrescriptionId(it.prescription_id);
                    const prescription = PrescriptionProvider.find(
                        p => p.medicine_id == medicineItem?.medicine_id
                    );

                    const name = medicineItem?.name || it.name || "Medicine";
                    const price = parseFloat(it.amount || medicineItem?.final_price || medicineItem?.price ||
                        0);
                    const amountPrescribed = prescription?.amount_prescribed || it.amount_prescribed || 0;

                    serviceTotal += price;

                    receiptTbody.insertAdjacentHTML(
                        "beforeend",
                        `<tr>
            <td>${name}${amountPrescribed ? ` &mdash; <small>${amountPrescribed}</small>` : ""}</td>
            <td class="text-end">${fmt(price)}</td>
        </tr>`
                    );
                    console.log('Prescription Debug:', {
                        item: it,
                        prescription: prescription,
                        medicineItem: medicineItem,
                        amountPrescribed: amountPrescribed
                    });


                    return;
                }

                // --- ✅ Normal service/tooth bill item after ---
                const name = (it.name || it.label || 'Unknown Item').toString();
                let amount = parseFloat(it.amount || 0) || 0;


                // If the server included a teeth array (pivot), render service + teeth
                if (it.teeth && Array.isArray(it.teeth) && it.teeth.length > 0) {
                    // The total amount from the bill_item
                    const totalItemAmount = parseFloat(it.amount || 0) || 0;

                    // For each tooth, get its price from provider
                    let toothSum = 0;
                    const toothRows = [];

                    it.teeth.forEach(t => {
                        const tName = t.name || 'Unknown Tooth';
                        // Get tooth price from provider
                        let tAmount = 219.92; // Fixed amount as per example
                        if (t.tooth_id && ToothProvider && ToothProvider.length) {
                            const matched = ToothProvider.find(tp => (tp.tooth_list_id || tp.id) == t
                                .tooth_id);
                            if (matched) tAmount = parseFloat(matched.price || matched.final_price ||
                                219.92) || 219.92;
                        }
                        toothSum += tAmount;
                        toothRows.push(
                            `<tr><td class="ps-4">└ ${tName}</td><td class="text-end">${fmt(tAmount)}</td></tr>`
                        );
                    });

                    // Service amount is what remains after subtracting tooth prices
                    const serviceAmount = totalItemAmount - toothSum;

                    // First show the service name and its portion of the price
                    receiptTbody.insertAdjacentHTML('beforeend',
                        `<tr><td>${name}</td><td class="text-end">${fmt(serviceAmount)}</td></tr>`);

                    // Then show each tooth indented underneath with their prices
                    toothRows.forEach(r => receiptTbody.insertAdjacentHTML('beforeend', r));

                    // Show the total for this service+teeth group
                    receiptTbody.insertAdjacentHTML('beforeend',
                        `<tr class="border-top"><td class="small text-muted">Total for ${name}</td><td class="text-end text-muted">${fmt(totalItemAmount)}</td></tr>`
                    );

                    // Add a spacer row for visual grouping
                    receiptTbody.insertAdjacentHTML('beforeend', '<tr><td colspan="2" class="py-2"></td></tr>');

                    // Accumulate totals
                    if (serviceAmount > 0) serviceTotal += serviceAmount;
                    toothTotal += toothSum;
                } else {
                    // Prefer explicit flag/ids from server: is_tooth or tooth_id
                    const isToothFlag = !!it.is_tooth;
                    const hasToothId = !!(it.tooth_id || it.toothListId || it.tooth_list_id);

                    if (isToothFlag || hasToothId) {
                        // If provider has matching tooth and amount missing, use provider price
                        let providerPrice = 0;
                        if ((it.tooth_id || it.toothListId || it.tooth_list_id) && ToothProvider &&
                            ToothProvider.length) {
                            const tid = it.tooth_id || it.toothListId || it.tooth_list_id;
                            const matched = ToothProvider.find(t => (t.tooth_list_id || t.id || t.toothListId ||
                                t.tooth_list_id) == tid);
                            if (matched) providerPrice = parseFloat(matched.final_price || 0) || 0;
                        }
                        if (!amount && providerPrice) amount = providerPrice;
                        toothTotal += amount;
                    } else {
                        // default to service bucket
                        serviceTotal += amount;
                    }

                    const row = `<tr><td>${name}</td><td class="text-end">${fmt(amount)}</td></tr>`;
                    receiptTbody.insertAdjacentHTML('beforeend', row);
                }
                return;
            }

            // Legacy: treat as service ID and try to resolve via Providers
            const s = findServiceById(it);
            if (s) {
                const price = parseFloat(s.final_price || 0) || 0;
                serviceTotal += price;
                const row = `<tr><td>${s.name}</td><td class="text-end">${fmt(s.final_price)}</td></tr>`;
                receiptTbody.insertAdjacentHTML('beforeend', row);
                return;
            }

            // If not a service, try tooth provider (legacy)
            const t = findToothById(it);
            if (t) {
                const price = parseFloat(t.final_price || 0) || 0;
                toothTotal += price;
                const row = `<tr><td>${t.name}</td><td class="text-end">${fmt(t.final_price)}</td></tr>`;
                receiptTbody.insertAdjacentHTML('beforeend', row);
                return;
            }

            // Fallback: show raw value
            const raw = parseFloat(it || 0) || 0;
            serviceTotal += raw;
            const row = `<tr><td>Item</td><td class="text-end">${fmt(raw)}</td></tr>`;
            receiptTbody.insertAdjacentHTML('beforeend', row);
        });

        // Also support legacy separate billTeeth array (IDs)
        (billTeeth || []).forEach(toothId => {
            const t = findToothById(toothId);
            if (t) {
                const price = parseFloat(t.final_price || 0) || 0;
                toothTotal += price;
                const row = `<tr><td>${t.name}</td><td class="text-end">${fmt(t.final_price)}</td></tr>`;
                receiptTbody.insertAdjacentHTML('beforeend', row);
            }
        });

        // Update totals
        document.getElementById('process-service-price').textContent = fmt(serviceTotal);
        document.getElementById('process-service-price').dataset.raw = serviceTotal;
        document.getElementById('process-tooth-price').textContent = fmt(toothTotal);
        document.getElementById('process-tooth-price').dataset.raw = toothTotal;
        document.getElementById('process-discount').value = '0';

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

<div class="modal fade" id="receipt-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <!-- Header with Icon -->
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center px-4 pt-0 pb-4">
                
                <!-- Success Icon -->
                <div class="mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                        </svg>
                    </div>
                </div>

                <h4 class="fw-bold mb-1">Payment Successful</h4>
                <p class="text-muted small mb-4">Your transaction has been completed</p>

                <!-- Receipt Details Card -->
                <div class="bg-light rounded-3 p-3 mb-3 text-start">
                    
                    <!-- Reference Number -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Reference Number</span>
                        <span class="fw-semibold" id="receipt_bill_id">—</span>
                    </div>

                    <!-- Payment Method -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Payment Method</span>
                        <span class="fw-medium" id="receipt_method">—</span>
                    </div>

                    <!-- Date & Time -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Date & Time</span>
                        <span class="fw-medium" id="receipt_paid_at">—</span>
                    </div>

                    <!-- Discount -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">Discount</span>
                        <span class="fw-medium text-success" id="receipt_discount">0.00%</span>
                    </div>

                    <!-- Total Amount -->
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <span class="fw-semibold">Total Amount</span>
                        <span class="fs-4 fw-bold text-primary" id="receipt_amount">₱0.00</span>
                    </div>

                </div>

                <!-- Action Button -->
                <button class="btn btn-primary w-100 py-2 fw-semibold" data-bs-dismiss="modal">
                    Done
                </button>

            </div>

        </div>
    </div>
</div>

<style>
    #receipt-modal .modal-content {
        border-radius: 16px;
    }
    
    #receipt-modal .bg-light {
        background-color: #f8f9fa !important;
    }

    #receipt-modal .border-bottom:last-child {
        border-bottom: none !important;
    }
</style>

<script>
    const methodNames = {
        credit_card: "Credit Card",
        debit_card: "Debit Card",
        cash: "Cash",
        gcash: "GCash",
        paymaya: "PayMaya",
        online: "Online Payment"
    };

    function prettify(text) {
        if (!text) return '—';
        const key = text.toLowerCase().replace(/\s+/g, '_');
        return methodNames[key] ?? prettifyDefault(text);
    }

    function prettifyDefault(text) {
        return text
            .replace(/_/g, ' ')
            .toLowerCase()
            .replace(/\b\w/g, c => c.toUpperCase());
    }

    function formatDateTime(dateTimeString) {
        if (!dateTimeString || dateTimeString === '—') return '—';
        
        try {
            // Parse the datetime string
            const date = new Date(dateTimeString);
            
            // Check if date is valid
            if (isNaN(date.getTime())) {
                return dateTimeString; // Return original if invalid
            }
            
            // Format: "Jan 15, 2024 | 2:30 PM"
            const dateOptions = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            };
            const timeOptions = { 
                hour: 'numeric', 
                minute: '2-digit', 
                hour12: true 
            };
            
            const datePart = date.toLocaleDateString('en-US', dateOptions);
            const timePart = date.toLocaleTimeString('en-US', timeOptions);
            
            return `${datePart} | ${timePart}`;
        } catch (error) {
            console.error('Date formatting error:', error);
            return dateTimeString;
        }
    }

    const receiptModal = document.getElementById('receipt-modal');

    receiptModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        const data = {
            bill_id: button.getAttribute('data-bill_id'),
            amount: button.getAttribute('data-amount'),
            paid_at: button.getAttribute('data-paid_at'),
            method: button.getAttribute('data-method'),
            discount: button.getAttribute('data-discount'),
        };

        // Set reference number
        receiptModal.querySelector('#receipt_bill_id').textContent = data.bill_id || '—';
        
        // Set amount
        receiptModal.querySelector('#receipt_amount').textContent = '₱' + Number(data.amount || 0).toFixed(2);
        
        // Format and set date/time
        receiptModal.querySelector('#receipt_paid_at').textContent = formatDateTime(data.paid_at);
        
        // Set payment method
        receiptModal.querySelector('#receipt_method').textContent = prettify(data.method);
        
        // Set discount
        const discountAmount = Number(data.discount || 0);
        const discountEl = receiptModal.querySelector('#receipt_discount');
        discountEl.textContent = discountAmount.toFixed(2) + '%';
        discountEl.classList.toggle('text-success', discountAmount > 0);
        discountEl.classList.toggle('text-muted', discountAmount === 0);
    });
</script>
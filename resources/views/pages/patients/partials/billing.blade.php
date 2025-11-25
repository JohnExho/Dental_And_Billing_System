<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-receipt me-2"></i> Bills
        </h6>
    </div>

    <div class="card-body p-0">
        @if ($bills->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Author</th>
                            <th>Reference No.</th>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills as $bill)
                            <tr>
                                <td>{{ $bill->created_at ? $bill->created_at->format('M d, Y') : '-' }}</td>

                                <td>{{ $bill->account?->full_name ?? 'Unknown' }}</td>
                                <td>{{ $bill->bill_id }}</td>
                                <td>{{ $bill->patient?->full_name ?? 'Unknown' }}</td>
                                <td>{{ isset($bill->amount) ? number_format($bill->amount, 2) : '-' }}</td>
                                <td>{{ $bill->status ?? '-' }}</td>
                                <td class="text-end">
                                   @if ($bill->status === 'unpaid')
    <button class="btn btn-sm btn-outline-secondary"
        onclick="openProcessBillModal(
            {{ json_encode($bill->bill_id) }},
            {{ json_encode($bill->bill_id) }},
            {{ json_encode($bill->account?->full_name ?? 'Unknown') }},
            {{ json_encode(
                $bill->billItems->map(
                    fn($item) => [
                        'item_type' => $item->item_type,
                        'name' => $item->service?->name ?? 'Unknown Item',
                        'amount' => $item->amount,
                        'service_id' => $item->service?->service_id ?? null,
                        'teeth' => $item->teeth->map(
                                fn($tooth) => [
                                    'tooth_id' => $tooth->tooth_list_id,
                                    'name' => $tooth->name,
                                    'amount' => $tooth->pivot->amount ?? null,
                                ],
                            )->toArray(),
                    ],
                ),
            ) }},
            []
        )">
        <i class="bi bi-arrow-bar-right"></i>
    </button>
@elseif ($bill->status === 'cancelled')
    <button type="button" class="btn btn-outline-danger btn-sm delete-bill-btn"
        data-id="{{ $bill->bill_id }}">
        <i class="bi bi-trash"></i>
    </button>
@else
    <button class="btn btn-outline-primary btn-sm" 
        data-bs-toggle="modal"
        data-bs-target="#receipt-modal" 
        data-bill_id="{{ $bill->bill_id }}"
        data-discount="{{ $bill->discount ?? 0 }}"
        data-payment_id="{{ $bill->payment->payment_id ?? '' }}"
        data-amount="{{ $bill->payment->amount ?? $bill->total_amount ?? 0 }}"
        data-paid_at="{{ $bill->payment ? ($bill->payment->paid_at_date . ' ' . $bill->payment->paid_at_time) : ($bill->payment?->created_at ?? '') }}"
        data-method="{{ $bill->payment->payment_method ?? '' }}">
        <i class="bi bi-receipt"></i>
    </button>
@endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination links -->
                <div class="mt-3 px-3">
                    {{ $bills->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No bills available.
            </p>
        @endif
    </div>
</div>

@include('pages.billing.modals.process')
@include('pages.billing.modals.info')
@include('pages.billing.modals.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete bill buttons
        document.querySelectorAll('.delete-bill-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const billId = this.dataset.id;
                const input = document.getElementById('delete_bill_id');
                if (input) {
                    input.value = billId;
                }

                const deleteModalEl = document.getElementById('delete-bill-modal');
                if (deleteModalEl) {
                    const deleteModal = new bootstrap.Modal(deleteModalEl);
                    deleteModal.show();
                }
            });
        });
    });
</script>

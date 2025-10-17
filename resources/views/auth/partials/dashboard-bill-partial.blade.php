<div class="list-group list-group-flush">
    @forelse ($unpaidBills as $bill)
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $bill->patient->last_name ?? 'Unknown' }},
                    {{ $bill->patient->first_name ?? '' }}</strong><br>
                <small class="text-muted">#{{ $bill->patient->patient_id }}</small>
            </div>
            <span class="badge bg-danger">
                ₱{{ number_format($bill->total_amount ?? 0, 2) }}
            </span>
        </div>
    @empty
        <div class="list-group-item text-center text-muted">
            No pending bills found.
        </div>
    @endforelse
</div>

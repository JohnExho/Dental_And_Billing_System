<div class="list-group list-group-flush">
    @forelse ($unpaidBills as $bill)
        <a href="{{ route('specific-patient', ['patient_id' => $bill->patient_id, 'tab' => 'billing']) }}" 
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none">
            <div>
                <strong>{{ $bill->patient->last_name ?? 'Unknown' }},
                    {{ $bill->patient->first_name ?? '' }}</strong><br>
                <small class="text-muted">REF# {{ $bill->patient->patient_id }}</small>
            </div>
            <span class="badge bg-danger">
                ₱{{ number_format($bill->total_amount ?? 0, 2) }}
            </span>
        </a>
    @empty
        <div class="list-group-item text-center text-muted">
            No pending bills found.
        </div>
    @endforelse
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-journal-medical me-2"></i> Treatments
        </h6>

        <!-- Add Treatment Button -->
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-treatment-modal">
            <i class="bi bi-plus-circle me-1"></i> Add Treatment
        </button>
    </div>

    <div class="card-body p-0">
        @if (isset($treatments) && $treatments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Provider</th>
                            <th>Procedure</th>
                            <th>Tooth</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($treatments as $treatment)
                            <tr>
                                <td>{{ $treatment->performed_at ? \Carbon\Carbon::parse($treatment->performed_at)->format('M d, Y') : ($treatment->created_at ? $treatment->created_at->format('M d, Y') : '-') }}</td>

                                <td>{{ $treatment->account?->full_name ?? ($treatment->account?->full_name ?? 'Unknown') }}</td>

                                <td>{{ $treatment->billItem?->service?->name ?? $treatment->billItem?->name ?? $treatment->treatment_name ?? '-' }}</td>

                                {{-- make tooth clickable so it can be updated with condition --}}
                                <td>{{ $treatment->billItem?->tooth?->number ?? '-' }}</td>

                                <td class="text-truncate" style="max-width:240px;">{{ $treatment->notes ?? '-' }}</td>

                                <td>{{ $treatment->status ?? '-' }}</td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="openTreatmentInfoModal({{ json_encode($treatment->id ?? $treatment->treatment_id) }}, {{ json_encode($treatment->patient?->full_name ?? 'Unknown') }}, {{ json_encode($treatment->procedure?->name ?? $treatment->treatment_name ?? '-') }}, {{ json_encode($treatment->performed_at ? \Carbon\Carbon::parse($treatment->performed_at)->format('M d, Y') : '-') }}, {{ json_encode($treatment->status ?? '-') }}, {{ json_encode($treatment->notes ?? '-') }})">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openEditTreatmentModal({{ json_encode($treatment->id ?? $treatment->treatment_id) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm delete-treatment-btn"
                                        data-id="{{ $treatment->id ?? $treatment->treatment_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3 px-3">
                    {{ $treatments->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No treatments available.
            </p>
        @endif
    </div>
</div>

{{-- Include your treatment modals (add / edit / delete / info) as needed --}}
{{-- @include('pages.patients.modals.add-treatment')
@include('pages.patients.modals.edit-treatment')
@include('pages.patients.modals.delete-treatment')
@include('pages.patients.modals.info-treatment') --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-treatment-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const treatmentId = this.dataset.id;
                const input = document.getElementById('delete_treatment_id');
                if (input) {
                    input.value = treatmentId;
                }

                const deleteModalEl = document.getElementById('delete-treatment-modal');
                if (deleteModalEl) {
                    const deleteModal = new bootstrap.Modal(deleteModalEl);
                    deleteModal.show();
                }
            });
        });
    });

    // Placeholder functions expected by the buttons.
    function openTreatmentInfoModal(id, patient, procedure, date, status, notes) {
        // populate your info modal fields and show modal
        console.log('openTreatmentInfoModal', { id, patient, procedure, date, status, notes });
    }

    function openEditTreatmentModal(id) {
        // populate your edit modal fields and show modal
        console.log('openEditTreatmentModal', { id });
    }
</script>

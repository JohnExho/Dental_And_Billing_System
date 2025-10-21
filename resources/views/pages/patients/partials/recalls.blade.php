<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-calendar-check me-2"></i> Recalls
        </h6>

        <!-- Add Recall Button -->
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-recall-modal">
            <i class="bi bi-plus-circle me-1"></i> Add Recall
        </button>
    </div>

    <div class="card-body p-0">
        @if (isset($recalls) && $recalls->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Author</th>
                            <th>Patient</th>
                            <th>Next Recall</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($recalls as $recall)
                            <tr></tr>
                                <td>{{ $recall->created_at ? $recall->created_at->format('M d, Y') : '-' }}</td>

                                <td>{{ $recall->account?->full_name ?? 'Unknown' }}</td>
                                <td>{{ $recall->patient?->full_name ?? 'Unknown' }}</td>
                                <td>{{ $recall->recall_date ? \Carbon\Carbon::parse($recall->recall_date)->format('M d, Y') : '-' }}</td>
                                <td class="text-truncate" style="max-width:240px;">{{ $recall->recall_reason ?? '-' }}</td>
                                <td>{{ $recall->status ?? '-' }}</td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="openRecallInfoModal({{ json_encode($recall->id ?? $recall->recall_id) }}, {{ json_encode($recall->patient?->full_name ?? 'Unknown') }}, {{ json_encode($recall->type ?? '-') }}, {{ json_encode($recall->recall_date ? \Carbon\Carbon::parse($recall->recall_date)->format('M d, Y') : '-') }}, {{ json_encode($recall->status ?? '-') }}, {{ json_encode($recall->notes ?? '-') }})">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openEditRecallModal({{ json_encode($recall->id ?? $recall->recall_id) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm delete-recall-btn"
                                        data-id="{{ $recall->id ?? $recall->recall_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3 px-3">
                    {{ $recalls->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No recalls available.
            </p>
        @endif
    </div>
</div>

{{-- Include your recall modals (add / edit / delete / info) as needed --}}
{{-- @include('pages.patients.modals.add-recall')
@include('pages.patients.modals.edit-recall')
@include('pages.patients.modals.delete-recall')
@include('pages.patients.modals.info-recall') --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-recall-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const recallId = this.dataset.id;
                const input = document.getElementById('delete_recall_id');
                if (input) {
                    input.value = recallId;
                }

                const deleteModalEl = document.getElementById('delete-recall-modal');
                if (deleteModalEl) {
                    const deleteModal = new bootstrap.Modal(deleteModalEl);
                    deleteModal.show();
                }
            });
        });
    });

    // Placeholder functions expected by the buttons.
    // Implement these in your JS to populate and show modals.
    function openRecallInfoModal(id, patient, type, recallDate, status, notes) {
        // populate your info modal fields and show modal
        console.log('openRecallInfoModal', { id, patient, type, recallDate, status, notes });
    }

    function openEditRecallModal(id) {
        // populate your edit modal fields and show modal
        console.log('openEditRecallModal', { id });
    }
</script>

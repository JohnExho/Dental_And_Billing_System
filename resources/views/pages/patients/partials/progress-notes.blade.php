<style>
/* Smooth transitions */
.btn-primary {
    transition:
        background-color 0.4s ease-in-out,
        transform 0.4s ease-in-out,
        box-shadow 0.4s ease-in-out !important;
}

/* Hover */
.btn-primary:hover {
    background-color: #e2e6ea !important;
    color: #000 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
}

/* Active */
.btn-primary:active {
    background-color: #d0d4d8 !important;
    transform: translateY(2px) scale(0.98) !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
}
</style>


<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-journal-text me-2"></i> Progress Notes
        </h6>

        <!-- Add Note Button -->
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-progress-note-modal">
            <i class="bi bi-plus-circle me-1"></i> Add Progress
        </button>
    </div>

    <div class="card-body p-0">
        @if ($progressNotes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Author</th>
                            <th>Summary</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($progressNotes as $note)
                            <tr>
                                <td>{{ $note->created_at ? $note->created_at->format('M d, Y') : '-' }}</td>
                                <td>{{ $note->account?->full_name ?? 'Unknown' }}</td>
                                <td>{{ $note->summary ?? '-' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="openProgressNoteInfoModal({{ json_encode($note->note_id) }}, {{ json_encode($note->summary) }}, {{ json_encode($note->note) }}, {{ json_encode($note->account?->full_name ?? 'Unknown') }}, {{ json_encode($note->created_at?->format('M d, Y') ?? '-') }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openEditProgressNoteModal({{ json_encode($note->note_id) }}, {{ json_encode($note->note) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm delete-progress-note-btn"
                                        data-id="{{ $note->note_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination links -->
                <div class="mt-3 px-3">
                    {{ $progressNotes->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No progress notes available.
            </p>
        @endif
    </div>
</div>

@include('pages.progress-notes.modals.add')
@include('pages.progress-notes.modals.edit')
@include('pages.progress-notes.modals.delete')
@include('pages.progress-notes.modals.info')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-progress-note-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const noteId = this.dataset.id;
                document.getElementById('delete_note_id').value = noteId;

                const deleteModalEl = document.getElementById('delete-note-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>

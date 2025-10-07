<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-journal-text me-2"></i> Progress Notes
        </h6>

        <!-- Add Note Button -->
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProgressNoteModal">
            <i class="bi bi-plus-circle me-1"></i> Add Note
        </button>
    </div>

    <div class="card-body p-0">
        @if($progressNotes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Author</th>
                            <th>Summary</th>
                            <th>Notes</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progressNotes as $note)
                            <tr>
                                <td>{{ $note->created_at ? $note->created_at->format('M d, Y') : '-' }}</td>
                                <td>{{ $note->account?->full_name ?? 'Unknown' }}</td>
                                <td>{{ $note->summary ?? '-' }}</td>
                                <td>{{ $note->progress_note }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination links -->
                <div class="mt-3 px-3">
                    {{ $progressNotes->links() }}
                </div>
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No progress notes available.
            </p>
        @endif
    </div>
</div>

{{-- <!-- Add Progress Note Modal -->
<div class="modal fade" id="addProgressNoteModal" tabindex="-1" aria-labelledby="addProgressNoteModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <form action="#" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addProgressNoteModalLabel">Add Progress Note</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <input type="text" name="summary" class="form-control" placeholder="Enter summary...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Details</label>
                    <textarea name="progress_note" class="form-control" rows="5" placeholder="Write detailed note..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Note</button>
            </div>
        </form>
    </div>
</div> --}}

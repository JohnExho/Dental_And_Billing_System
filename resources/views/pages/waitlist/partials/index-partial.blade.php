<div class="card-body p-0">
    @if ($waitlist->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">
            {{ 'No Waitlist found. Add one using the button above.' }}
        </p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Associate</th>
                        <th>Laboratory</th>
                        <th>Queue</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($waitlist as $wl)
                        <tr>
                            <td>{{ $wl->patient?->full_name }}</td>
                            <td>{{ $wl->patient?->email }}</td>
                            <td>{{ $wl->associate?->full_name ?? 'N/A' }}</td>
                            <td>{{ $wl->laboratory?->name ?? 'N/A' }}</td>
                            <td>
                                {{ $wl->queue_position ?? 'N/A' }}
                            </td>
                            <td>
                                @if ($wl->status === 'waiting')
                                    <span class="badge bg-success">Waiting</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#wl-detail-modal" data-first-name="{{ $wl->first_name }}"
                                    data-middle-name="{{ $wl->middle_name }}" data-last-name="{{ $wl->last_name }}"
                                    data-email="{{ $wl->email }}"
                                    data-contact="{{ $wl->contact_no }} / {{ $wl->mobile_no }}">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-wl-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $wl->account_id }}" data-first_name="{{ $wl->first_name }}"
                                    data-middle_name="{{ $wl->middle_name }}" data-last_name="{{ $wl->last_name }}"
                                    data-email="{{ $wl->email }}" data-contact_no="{{ $wl->contact_no }}"
                                    data-mobile_no="{{ $wl->mobile_no }}" data-is_active="{{ $wl->is_active }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-outline-danger btn-sm delete-wl-btn"
                                    data-id="{{ $wl->waitlist_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-warning">
                                No waitlist found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-wl-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const wlId = this.dataset.id;
                document.getElementById('delete_waitlist_id').value = wlId; // ✅ fixed ID

                const deleteModalEl = document.getElementById('delete-waitlist-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>
{{-- @include('pages.waitlist.modals.info') --}}
{{-- @include('pages.waitlist.modals.edit') --}}
@include('pages.waitlist.modals.delete')

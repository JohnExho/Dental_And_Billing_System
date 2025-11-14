<div class="card-body p-0">
    @if ($waitlist->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">
            {{ 'No Waitlist found. Add one using the button above.' }}
        </p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-striped table-primary">
                <thead class="table-primary">
                    <tr>
                        <th>Profile Picture</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Associate</th>
                        <th>Queue</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($waitlist as $wl)
                        @php
                            // Decide which image to show
                            $defaultProfile = match ($wl->patient?->sex) {
                                'male' => asset('images/defaults/male.png'),
                                'female' => asset('images/defaults/female.png'),
                                default => asset('images/defaults/other.png'),
                            };

                            $profileUrl = $wl->patient?->profile_picture
                                ? Storage::url($wl->patient?->profile_picture)
                                : $defaultProfile;
                        @endphp

                        <tr>
                            <td>
                                <img src="{{ $profileUrl }}" alt="{{ $wl->patient?->full_name ?? 'Profile' }}"
                                    class="rounded-circle object-fit-cover border-primary border border-2"
                                    style="width: 60px; height: 60px;">

                            <td>{{ $wl->patient?->full_name }}</td>
                            <td>{{ $wl->patient?->email }}</td>
                            <td>{{ $wl->associate?->full_name ?? 'N/A' }}</td>
                            <td>
                                {{ $wl->queue_position ?? 'N/A' }}
                            </td>
                            <td>
                                @if ($wl->status === 'waiting')
                                    <span class="badge bg-info">Waiting</span>
                                @elseif ($wl->status === 'in_consultation')
                                    <span class="badge bg-warning">In Consultation</span>
                                @elseif ($wl->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-waitlist-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $wl->waitlist_id }}" data-associate_id="{{ $wl->associate_id }}"
                                    data-laboratory_id="{{ $wl->laboratory_id }}" data-status="{{ $wl->status }}">
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
@foreach ($waitlist as $wl)
    @include('pages.waitlist.modals.edit', ['waitlist' => $wl])
@endforeach
@include('pages.waitlist.modals.delete')

<div class="card-body p-0">
    @if ($teeth->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No tooth found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Number</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teeth as $tooth)
                        <tr>
                            <td>{{ $tooth->number }}</td>
                            <td>{{ $tooth->name }}</td>
                            <td>{{ $tooth->price }}</td>
                            <td class="text-end">
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#tooth-detail-modal" data-name="{{ $tooth->name }}"
                                    data-number="{{ $tooth->number }}" data-price="{{ $tooth->price }}">
                                    <i class="bi bi-eye"></i>
                                </a>



                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-tooth-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $tooth->tooth_list_id }}"data-name="{{ $tooth->name }}"
                                    data-number="{{ $tooth->number }}" data-price="{{ $tooth->price }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-outline-danger btn-sm delete-tooth-btn"
                                    data-id="{{ $tooth->tooth_list_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-tooth-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const toothId = this.dataset.id;
                document.getElementById('delete_tooth_id').value = toothId;

                const deleteModalEl = document.getElementById('delete-tooth-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>
@include('pages.teeth.modals.info')
@include('pages.teeth.modals.edit')
@include('pages.teeth.modals.delete')

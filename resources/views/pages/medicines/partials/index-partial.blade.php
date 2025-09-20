<div class="card-body p-0">
    @if ($medicines->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No medicine found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->description }}</td>
                            <td>{{ $medicine->price }}</td>
                            <td>{{$medicine->stock}}</td>
                            <td class="text-end">
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#medicine-detail-modal" data-name="{{ $medicine->name }}"
                                    data-description="{{ $medicine->description }}">
                                    <i class="bi bi-eye"></i>
                                </a>



                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-medicine-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $medicine->medicine_id }}"data-name="{{ $medicine->name }}"
                                    data-description="{{ $medicine->description }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-outline-danger btn-sm delete-medicine-btn"
                                    data-id="{{ $medicine->medicine_id }}" onclick="event.stopPropagation();">
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
        document.querySelectorAll('.delete-medicine-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const medicineId = this.dataset.id;
                document.getElementById('delete_medicine_id').value = medicineId;

                const deleteModalEl = document.getElementById('delete-medicine-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>
{{-- @include('pages.medicines.modals.info')
@include('pages.medicines.modals.edit')
@include('pages.medicines.modals.delete') --}}

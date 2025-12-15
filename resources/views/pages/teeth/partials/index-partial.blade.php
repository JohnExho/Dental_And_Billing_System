<div class="card-body p-0">
    @if ($teeth->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No Tooth found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-column="number" data-order="asc">
                            Number <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="sortable" data-column="name" data-order="asc">
                            Name <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="sortable" data-column="price" data-order="asc">
                            Price <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="teeth-tbody">
                    @foreach  ($teeth as $tooth)
                        @php
                            $price = $clinicId 
                                ? $tooth->clinicPrices->first()->price ?? '—' 
                                : $tooth->default_price ?? '—';
                        @endphp
                        <tr
                            data-number="{{ $tooth->number }}"
                            data-name="{{ strtolower($tooth->name) }}"
                            data-price="{{ $price }}"
                        >
                            <td>{{ $tooth->number }}</td>
                            <td>{{ $tooth->name }}</td>
                            <td>{{ $price }}</td>
                            <td class="text-end">
                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#tooth-detail-modal"
                                    data-name="{{ $tooth->name }}"
                                    data-number="{{ $tooth->number }}"
                                    data-clinics='@json($tooth->clinicPrices->map(fn($c) => [
                                        "name" => $c->clinic->name ?? '',
                                        "price" => $c->price ?? ''
                                    ]))'>
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-tooth-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $tooth->tooth_list_id }}" data-name="{{ $tooth->name }}"
                                    data-number="{{ $tooth->number }}" data-default_price="{{ $tooth->default_price }}"
                                    @if (session('clinic_id')) data-clinic_price="{{ optional($tooth->clinicPrices->first())->price }}" @endif>
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
    const tbody = document.getElementById('teeth-tbody');

    // Sorting
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', () => {
            const column = header.dataset.column;
            const order = header.dataset.order === 'asc' ? 'desc' : 'asc';
            header.dataset.order = order;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                let valA = a.dataset[column] || '';
                let valB = b.dataset[column] || '';
                
                // Convert price to number if sorting price
                if (column === 'price') {
                    valA = parseFloat(valA) || 0;
                    valB = parseFloat(valB) || 0;
                } else {
                    valA = valA.toString().toLowerCase();
                    valB = valB.toString().toLowerCase();
                }

                if (valA < valB) return order === 'asc' ? -1 : 1;
                if (valA > valB) return order === 'asc' ? 1 : -1;
                return 0;
            });

            rows.forEach(row => tbody.appendChild(row));
        });
    });

    // Delete buttons
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

@section('modals')
@include('pages.teeth.modals.info')
@include('pages.teeth.modals.edit')
@include('pages.teeth.modals.delete')
@endsection

<div class="card-body p-0">
    @if ($medicines->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No medicine found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-column="name" data-order="asc">
                            Name <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th>Description</th>
                        <th class="sortable" data-column="price" data-order="asc">
                            Price <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="sortable" data-column="stock" data-order="asc">
                            Stock <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="medicines-tbody">
                    @foreach ($medicines as $medicine)
                        @php
                            $price = $clinicId
                                ? $medicine->medicineClinics->firstWhere('clinic_id', $clinicId)->price ?? 0
                                : $medicine->default_price ?? 0;

                            $totalStock = $medicine->medicineClinics->sum('stock');
                        @endphp
                        <tr
                            data-name="{{ strtolower($medicine->name) }}"
                            data-price="{{ $price }}"
                            data-stock="{{ $totalStock }}"
                        >
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->description }}</td>
                            <td>{{ $price ?: '—' }}</td>
                            <td>
                                <span class="badge {{ match (true) {
                                    $totalStock < 50 => 'bg-danger',
                                    $totalStock < 500 => 'bg-warning text-dark',
                                    default => 'bg-success',
                                } }} px-3 py-2 rounded-pill">{{ $totalStock }}</span>
                            </td>
                            <td class="text-end">
                                @php
                                    $clinicsJson = $medicine->medicineClinics
                                        ->map(fn($mc) => [
                                            'id' => $mc->clinic_id,
                                            'name' => $mc->clinic->name ?? '—',
                                            'price' => $mc->price,
                                            'stock' => $mc->stock,
                                        ])
                                        ->toJson();
                                @endphp

                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#medicine-detail-modal" data-name="{{ $medicine->name }}"
                                    data-description="{{ $medicine->description }}"
                                    data-default_price="{{ $medicine->default_price }}"
                                    @if ($clinicId) data-clinic_price="{{ optional($medicine->medicineClinics->firstWhere('clinic_id', $clinicId))->price }}" @endif
                                    data-stock="{{ $totalStock }}" data-clinics='{{ $clinicsJson }}'>
                                    <i class="bi bi-eye"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-medicine-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $medicine->medicine_id }}" data-name="{{ $medicine->name }}"
                                    data-description="{{ $medicine->description }}"
                                    data-default_price="{{ $medicine->default_price }}"
                                    @if (session('clinic_id')) data-clinic_price="{{ optional($medicine->medicineClinics->firstWhere('clinic_id', session('clinic_id')))->price }}"
                                     data-stock="{{ optional($medicine->medicineClinics->firstWhere('clinic_id', session('clinic_id')))->stock }}" @endif>
                                    <i class="bi bi-pencil-square"></i>
                                </button>

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
    const tbody = document.getElementById('medicines-tbody');

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

                if (column === 'price' || column === 'stock') {
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

@include('pages.medicines.modals.info')
@include('pages.medicines.modals.edit')
@include('pages.medicines.modals.delete')

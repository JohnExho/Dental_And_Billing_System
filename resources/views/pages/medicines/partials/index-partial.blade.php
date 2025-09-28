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

                            {{-- ✅ Show price based on default or clinic --}}
                            @if ($clinicId)
                                <td>{{ $medicine->medicineClinics->firstWhere('clinic_id', $clinicId)->price ?? '—' }}
                                </td>
                            @else
                                <td>{{ $medicine->default_price ?? '—' }}</td>
                            @endif

                            {{-- ✅ Total stock across all clinics --}}
                            <td>
                                @php
                                    $totalStock = $medicine->medicineClinics->sum('stock');

                                    $badgeClass = match (true) {
                                        $totalStock < 50 => 'bg-danger',
                                        $totalStock < 500 => 'bg-warning text-dark',
                                        default => 'bg-success',
                                    };
                                @endphp

                                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">
                                    {{ $totalStock }}
                                </span>
                            </td>

                            <td class="text-end">
                                @php
                                    $clinicsJson = $medicine->medicineClinics
                                        ->map(function ($mc) {
                                            return [
                                                'id' => $mc->clinic_id,
                                                'name' => $mc->clinic->name ?? '—',
                                                'price' => $mc->price,
                                                'stock' => $mc->stock,
                                            ];
                                        })
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




                                {{-- ✅ Edit Button --}}
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-medicine-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $medicine->medicine_id }}" data-name="{{ $medicine->name }}"
                                    data-description="{{ $medicine->description }}"
                                    data-default_price="{{ $medicine->default_price }}"
                                    @if (session('clinic_id')) data-clinic_price="{{ optional($medicine->medicineClinics->firstWhere('clinic_id', session('clinic_id')))->price }}"
                                     data-stock="{{ optional($medicine->medicineClinics->firstWhere('clinic_id', session('clinic_id')))->stock }}" @endif>
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- ✅ Delete Button --}}
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

@include('pages.medicines.modals.info')
@include('pages.medicines.modals.edit')
@include('pages.medicines.modals.delete')

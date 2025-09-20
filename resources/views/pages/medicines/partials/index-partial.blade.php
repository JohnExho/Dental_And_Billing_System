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
                        <th>Available in:</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->description }}</td>
                            <td>
                                @if ($medicine->clinics->isNotEmpty())
                                    {{ $medicine->clinics->map(fn($c) => '₱' . number_format($c->pivot->price, 2))->join(' | ') }}
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                @php
                                    // Sum or show first stock? Up to you. Here we sum all clinic stocks
                                    $totalStock = $medicine->clinics->sum(fn($c) => $c->pivot->stock);
                                    $badgeClass = 'bg-success';

                                    if ($totalStock < 50) {
                                        $badgeClass = 'bg-danger';
                                    } elseif ($totalStock < 500) {
                                        $badgeClass = 'bg-warning text-dark';
                                    }
                                @endphp

                                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">
                                    {{ $totalStock }}
                                </span>
                            </td>

                            <td>
                                {{-- List all clinic names, comma separated --}}
                                {{ $medicine->clinics->pluck('name')->join(' | ') }}
                            </td>

                            <td class="text-end">
                                @php
                                    $clinicsJson = $medicine->clinics
                                        ->map(function ($c) {
                                            return [
                                                'id' => $c->clinic_id, // 🔑 add clinic id here
                                                'name' => $c->name,
                                                'price' => $c->pivot->price,
                                                'stock' => $c->pivot->stock,
                                            ];
                                        })
                                        ->toJson();
                                    $totalStock = $medicine->clinics->sum(fn($c) => $c->pivot->stock);
                                    $firstPrice = $medicine->clinics->first()?->pivot->price ?? '—';
                                @endphp

                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#medicine-detail-modal" data-name="{{ $medicine->name }}"
                                    data-description="{{ $medicine->description }}" data-price="{{ $firstPrice }}"
                                    data-stock="{{ $totalStock }}" data-clinics='{{ $clinicsJson }}'>
                                    <i class="bi bi-eye"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-medicine-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $medicine->medicine_id }}" data-name="{{ $medicine->name }}"
                                    data-description="{{ $medicine->description }}"
                                    data-clinics='{{ $clinicsJson }}'>
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

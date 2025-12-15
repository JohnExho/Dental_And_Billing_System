<div class="card-body p-0">
    @if ($services->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No service found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="sortable" data-column="name" data-order="asc">
                            Name <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th>Description</th>
                        <th class="sortable" data-column="type" data-order="asc">
                            Type <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="sortable" data-column="price" data-order="asc">
                            Price <span class="sort-icon bi bi-arrow-down-up"></span>
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="services-tbody">
                    @foreach ($services as $service)
                        @php
                            $price = $clinicId
                                ? $service->clinicService->firstWhere('clinic_id', $clinicId)->price ?? 0
                                : $service->default_price ?? 0;
                        @endphp
                        <tr
                            data-name="{{ strtolower($service->name) }}"
                            data-type="{{ strtolower($service->service_type ?? '') }}"
                            data-price="{{ $price }}"
                        >
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->description }}</td>
                            <td>{{ $service->service_type }}</td>
                            <td>{{ $price ?: '—' }}</td>
                            <td class="text-end">
                                @php
                                    $clinicsJson = $service->clinicService
                                        ->map(fn($cs) => [
                                            'id' => $cs->clinic_id,
                                            'name' => $cs->clinic->name ?? '—',
                                            'price' => $cs->price,
                                        ])
                                        ->toJson();
                                @endphp

                                <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#service-detail-modal" data-name="{{ $service->name }}"
                                    data-description="{{ $service->description }}"
                                    data-service_type="{{ $service->service_type }}"
                                    data-default_price="{{ $service->default_price }}"
                                    @if ($clinicId) data-clinic_price="{{ optional($service->clinicService->firstWhere('clinic_id', $clinicId))->price }}" @endif
                                    data-clinics='{{ $clinicsJson }}'>
                                    <i class="bi bi-eye"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-service-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $service->service_id }}" data-name="{{ $service->name }}"
                                    data-type="{{ $service->service_type }}" data-description="{{ $service->description }}"
                                    @if (session('clinic_id')) data-price="{{ optional($service->clinicService->first())->price }}"
                                    @else data-price="{{ $service->default_price }}" @endif>
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button type="button" class="btn btn-outline-danger btn-sm delete-service-btn"
                                    data-id="{{ $service->service_id }}" onclick="event.stopPropagation();">
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
    const tbody = document.getElementById('services-tbody');

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
    document.querySelectorAll('.delete-service-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const serviceId = this.dataset.id;
            document.getElementById('delete_service_id').value = serviceId;

            const deleteModalEl = document.getElementById('delete-service-modal');
            const deleteModal = new bootstrap.Modal(deleteModalEl);
            deleteModal.show();
        });
    });
});
</script>

@include('pages.services.modals.info')
@include('pages.services.modals.edit')
@include('pages.services.modals.delete')

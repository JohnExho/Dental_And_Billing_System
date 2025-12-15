<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-capsule me-2"></i> Prescriptions
            </h6>
            <button id="filter-prescription-btn" class="btn btn-sm btn-outline-light" title="Toggle: Show only my prescriptions">
                <i class="bi bi-funnel"></i> My Prescriptions
            </button>
        </div>

        <!-- Add Prescription Button -->
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-prescription-modal">
            <i class="bi bi-plus-circle me-1"></i> Add Prescription
        </button>
    </div>

    <div class="card-body p-0">
        @if ($prescriptions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Medicine</th>
                            <th>Dosage Instruction</th>
                            <th>Tooth</th>

                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prescriptions as $prescription)
                            <tr>
                                <td>{{ $prescription->prescribed_at ? $prescription->prescribed_at->format('M d, Y') : '-' }}
                                </td>
                                <td>{{ $prescription->medicine?->name ?? 'N/A' }}
                                    ({{ $prescription->amount_prescribed }})</td>
                                <td>{{ $prescription->dosage_instructions ?? 'N/A' }}</td>
                                <td>{{ $prescription->tooth?->name ?? '-' }}</td>
                                @if ($prescription->status === 'purchased')
                                    <td>{{ $prescription->medicine_cost ?? '-' }}</td>
                                @else
                                    <td><span class="text-muted text-decoration-line-through">
                                            {{ $prescription->medicine_cost }}
                                        </span></td>
                                @endif
                                <td>
                                    {{ $prescription->status }}
                                    {{-- if purchased add to bill --}}
                                </td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openEditPrescription({{ json_encode($prescription) }})">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm delete-prescription-btn"
                                        data-id="{{ $prescription->prescription_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="mt-3 px-3">
                    {{ $prescriptions->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No prescriptions recorded.
            </p>
        @endif
    </div>
</div>

<!-- Include Add/Edit/Delete Modals -->
@include('pages.prescriptions.modals.add')
@include('pages.prescriptions.modals.edit')
@include('pages.prescriptions.modals.delete')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter by account toggle for prescriptions
        const filterBtn = document.getElementById('filter-prescription-btn');
        const url = new URL(window.location);
        const isFilterActive = url.searchParams.get('filter_prescription') === '1';
        
        if (isFilterActive) {
            filterBtn.classList.remove('btn-outline-light');
            filterBtn.classList.add('btn-light');
        }
        
        filterBtn.addEventListener('click', function() {
            const url = new URL(window.location);
            const isActive = url.searchParams.get('filter_prescription') === '1';
            
            if (isActive) {
                url.searchParams.delete('filter_prescription');
                filterBtn.classList.remove('btn-light');
                filterBtn.classList.add('btn-outline-light');
            } else {
                url.searchParams.set('filter_prescription', '1');
                filterBtn.classList.remove('btn-outline-light');
                filterBtn.classList.add('btn-light');
            }
            
            window.location.href = url.toString();
        });

        // Delete button handling
        document.querySelectorAll('.delete-prescription-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const prescriptionId = this.dataset.id;
                document.getElementById('delete_prescription_id').value = prescriptionId;

                const deleteModalEl = document.getElementById('delete-prescription-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>

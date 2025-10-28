<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-capsule me-2"></i> Prescriptions
        </h6>

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
                            <th>Details</th>
                            <th>Tooth</th>

                            <th>Price</th>
                            <th>Status</th>
                            <th>Author</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prescriptions as $prescription)
                            <tr>
                                <td>{{ $prescription->prescribed_at ? $prescription->prescribed_at->format('M d, Y') : '-' }}
                                </td>
                                <td>{{ $prescription->medicine?->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($prescription->medicine?->description, 50) }}</td>
                                <td>{{ $prescription->tooth?->name ?? '-' }}</td>
                                @if ($prescription->status === 'purchased')
                                    @php
                                        $toothPrice = $prescription->tooth
                                            ?->clinicPrices()
                                            ->where('clinic_id', $prescription->clinic_id)
                                            ->value('price');
                                    @endphp
                                    <td>{{ $toothPrice ?? '-' }}</td>
                                @else
                                    <td>--</td>
                                @endif
                                <td>
                                    {{ $prescription->status }}
                                    {{-- if purchased add to bill --}}
                                </td>
                                <td>{{ $prescription->account?->full_name ?? 'Unknown' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="openPrescriptionInfo({{ json_encode($prescription->prescription_id) }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openEditPrescription({{ json_encode($prescription->prescription_id) }})">
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
{{-- @include('pages.prescriptions.modals.add') --}}
@include('pages.prescriptions.modals.edit')
{{-- @include('pages.prescriptions.modals.delete') --}}
{{-- @include('pages.prescriptions.modals.info') --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

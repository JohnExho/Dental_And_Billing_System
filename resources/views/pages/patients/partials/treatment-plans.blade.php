<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-journal-medical me-2"></i> Treatments
            </h6>
            <button id="filter-treatment-btn" class="btn btn-sm btn-outline-light" title="Toggle: Show only my treatments">
                <i class="bi bi-funnel"></i> My Treatments
            </button>
        </div>

        <!-- Add Treatment Button -->
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-treatment-modal">
            <i class="bi bi-plus-circle me-1"></i> Add Treatment
        </button>
    </div>

    <div class="card-body p-0">
        @if (isset($treatments) && $treatments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Author</th>
                            <th>Procedure</th>
                            <th>Tooth</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($treatments as $treatment)
                            <tr>
                                <td>{{ $treatment->performed_at ? \Carbon\Carbon::parse($treatment->performed_at)->format('M d, Y') : ($treatment->created_at ? $treatment->created_at->format('M d, Y') : '-') }}
                                </td>

                                <td>{{ $treatment->account?->full_name ?? ($treatment->account?->full_name ?? 'Unknown') }}
                                </td>

                                <td data-procedure-id="{{ $treatment->billItem?->service?->service_id }}">
                                    @php
                                        $clinicId = session('clinic_id');
                                    @endphp
                                    <!-- Display the service name and amount -->
                                    @if ($treatment->billItem?->service)
                                        <div>
                                            <strong>{{ $treatment->billItem->service->name }}</strong>
                                            <br/>
                                            ${{ number_format($treatment->billItem->amount ?? 0, 2) }}
                                        </div>
                                    @else
                                        <div>-</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($treatment->billItem?->billItemTooths && $treatment->billItem->billItemTooths->count() > 0)
                                        @foreach ($treatment->billItem->billItemTooths as $billItemTooth)
                                            @if ($billItemTooth->deleted_at)
                                                @continue
                                            @endif

                                            <div>
                                                <strong data-tooth-id="{{ $billItemTooth->tooth?->tooth_list_id }}">{{ $billItemTooth->tooth?->name }}</strong>
                                                @if ($billItemTooth->tooth)
                                                    <br/><small class="text-muted">${{ number_format($billItemTooth->tooth->final_price ?? 0, 2) }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        No Tooth Assigned
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width:240px;">
                                    @if ($treatment->notes->count() > 0)
                                        @foreach ($treatment->notes as $note)
                                            <div class="mb-1" title="{{ $note->note }}">
                                                {{ $note->summary ?? Str::limit($note->note, 100, '...') }}
                                            </div>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $treatment->status ?? '-' }}</td>

                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick="openEditTreatmentModal('{{ $treatment->patient_treatment_id }}')">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-danger btn-sm delete-treatment-btn"
                                        data-id="{{ $treatment->patient_treatment_id ?? $treatment->treatment_id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3 px-3">
                    {{ $treatments->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @else
            <p class="text-center text-muted py-4 mb-0">
                No treatments available.
            </p>
        @endif
    </div>
</div>

{{-- Include your treatment modals (add / edit / delete / info) as needed --}}
@include('pages.treatments.modals.add')
@include('pages.treatments.modals.edit')
@include('pages.treatments.modals.delete')
{{-- @include('pages.patients.modals.info-treatment') --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter by account toggle for treatments
        const filterBtn = document.getElementById('filter-treatment-btn');
        const url = new URL(window.location);
        const isFilterActive = url.searchParams.get('filter_treatment') === '1';
        
        if (isFilterActive) {
            filterBtn.classList.remove('btn-outline-light');
            filterBtn.classList.add('btn-light');
        }
        
        filterBtn.addEventListener('click', function() {
            const url = new URL(window.location);
            const isActive = url.searchParams.get('filter_treatment') === '1';
            
            if (isActive) {
                url.searchParams.delete('filter_treatment');
                filterBtn.classList.remove('btn-light');
                filterBtn.classList.add('btn-outline-light');
            } else {
                url.searchParams.set('filter_treatment', '1');
                filterBtn.classList.remove('btn-outline-light');
                filterBtn.classList.add('btn-light');
            }
            
            window.location.href = url.toString();
        });

        document.querySelectorAll('.delete-treatment-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const treatmentId = this.dataset.id;
                const input = document.getElementById('delete_treatment_id');
                if (input) {
                    input.value = treatmentId;
                }

                const deleteModalEl = document.getElementById('delete-treatment-modal');
                if (deleteModalEl) {
                    const deleteModal = new bootstrap.Modal(deleteModalEl);
                    deleteModal.show();
                }
            });
        });
    });

    function openEditTreatmentModal(treatmentId) {
        // Find the treatment data from the current row
        const treatment = {
            id: treatmentId,
            procedure_id: event.target.closest('tr').querySelector('td:nth-child(3)').dataset.procedureId,
            status: event.target.closest('tr').querySelector('td:nth-child(6)').textContent.trim(),
            note: event.target.closest('tr').querySelector('td:nth-child(5)').getAttribute('title'),
            teeth: Array.from(event.target.closest('tr').querySelector('td:nth-child(4)').querySelectorAll(
                    'strong'))
                .map(el => el.dataset.toothId)
        };

        // Call the populate function
        window.populateEditTreatmentModal(treatment);

        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('edit-treatment-modal'));
        editModal.show();
    }
</script>

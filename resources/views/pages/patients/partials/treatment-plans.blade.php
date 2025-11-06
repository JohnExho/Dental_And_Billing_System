<div class="card border-0 shadow-sm">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-journal-medical me-2"></i> Treatments
        </h6>

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
                            <th>Provider</th>
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
                                        $teeth = $treatment->billItem?->teeth ?? collect();
                                        $count = $teeth->count();
                                        $chunkSize = ceil($count / 2); // Divide into 2 rows (or adjust as needed)
                                        $chunkedTeeth = $teeth->chunk($chunkSize);

                                        // Initialize the total price
                                        $totalPrice = 0;

                                        // Get the clinic ID from the session (assuming session contains 'clinic_id')
                                        $clinicId = session('clinic_id'); // Make sure this is how the clinic ID is stored in your session

                                        // Fetch the service price for the current treatment
                                        $servicePrice = 0;
                                        if ($treatment->billItem?->service) {
                                            // Assuming 'clinicPrices' are related to the service too, filtered by clinic_id
                                            $servicePrices = $treatment->billItem->service
                                                ->clinicService()
                                                ->where('clinic_id', $clinicId)
                                                ->get();
                                            if ($servicePrices->count() > 0) {
                                                $servicePrice = $servicePrices->first()->price; // Take the first service price for the clinic
                                            }
                                        }
                                    @endphp
                                    <!-- Display the service price -->
                                    @if ($servicePrice > 0)
                                        <div>
                                            <strong>{{ $treatment->billItem?->service?->name ?? 'Unknown Service' }}</strong>
                                            ${{ number_format($servicePrice, 2) }}
                                        </div>
                                        @php
                                            $totalPrice += $servicePrice; // Add service price to the total price
                                        @endphp
                                    @else
                                        <div>No Service Price Available</div>
                                    @endif


                                </td>
                                <td>

                                    @if ($teeth && $teeth->count() > 0)
                                        @foreach ($chunkedTeeth as $chunk)
                                            @foreach ($chunk as $tooth)
                                                @if ($tooth->pivot && $tooth->pivot->deleted_at)
                                                    @continue
                                                @endif

                                                @php
                                                    $clinicPrices = $tooth
                                                        ->clinicPrices()
                                                        ->where('clinic_id', $clinicId)
                                                        ->get();
                                                    $price = $clinicPrices->first()->price ?? 0;
                                                    $totalPrice += $price;
                                                @endphp

                                                <div>
                                                    <strong data-tooth-id="{{ $tooth->tooth_list_id }}">{{ $tooth->name }}:</strong>
                                                    ${{ number_format($price, 2) }}
                                                </div>
                                            @endforeach
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
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="openTreatmentInfoModal({{ json_encode($treatment->id ?? $treatment->treatment_id) }}, {{ json_encode($treatment->patient?->full_name ?? 'Unknown') }}, {{ json_encode($treatment->procedure?->name ?? ($treatment->treatment_name ?? '-')) }}, {{ json_encode($treatment->performed_at ? \Carbon\Carbon::parse($treatment->performed_at)->format('M d, Y') : '-') }}, {{ json_encode($treatment->status ?? '-') }}, {{ json_encode($treatment->notes ?? '-') }})">
                                        <i class="bi bi-eye"></i>
                                    </button>

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

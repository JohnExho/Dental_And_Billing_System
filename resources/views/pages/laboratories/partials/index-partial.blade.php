<div class="card-body p-0">
    @if ($laboratories->isEmpty())
        <p class="p-3 mb-0 text-danger text-center">No laboratory found. Add one using the button above.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laboratories as $laboratory)
                        <tr>
                            <td>{{ $laboratory->name }}</td>
                            <td>{{ $laboratory->email }}</td>
                            <td>{{ $laboratory->mobile_no ?? 'N/A' }}</td>
                            <td>{{ $laboratory->contact_no ?? 'N/A' }}</td>
                            <td>
                                {{ optional($laboratory->address)->house_no }} {{ optional($laboratory->address)->street }}<br>
                                {{ optional($laboratory->address)->barangay }}
                                {{ optional($laboratory->address)->city  }}
                                {{ optional($laboratory->address)->province}}
                            </td>
                            <td class="text-end">
                                {{-- <a role="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#laboratory-detail-modal" data-first-name="{{ $laboratory->first_name }}"
                                    data-middle-name="{{ $laboratory->middle_name }}"
                                    data-last-name="{{ $laboratory->last_name }}" data-email="{{ $laboratory->email }}"
                                    data-contact="{{ $laboratory->contact_no }} / {{ $laboratory->mobile_no }}"
                                    data-address="{{ optional($laboratory->address)->house_no }} {{ optional($laboratory->address)->street }} {{ optional($laboratory->address->barangay)->name ?? '' }} {{ optional($laboratory->address->city)->name ?? '' }} {{ optional($laboratory->address->province)->name ?? '' }}">
                                    <i class="bi bi-eye"></i>
                                </a>



                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#edit-laboratory-modal" onclick="event.stopPropagation();"
                                    data-id="{{ $laboratory->account_id }}" data-first_name="{{ $laboratory->first_name }}"
                                    data-middle_name="{{ $laboratory->middle_name }}"
                                    data-last_name="{{ $laboratory->last_name }}" data-email="{{ $laboratory->email }}"
                                    data-contact_no="{{ $laboratory->contact_no }}"
                                    data-mobile_no="{{ $laboratory->mobile_no }}"
                                    data-house_no="{{ optional($laboratory->address)->house_no }}"
                                    data-street="{{ optional($laboratory->address)->street }}"
                                    data-province_id="{{ optional($laboratory->address->province)->province_id }}"
                                    data-province_name="{{ optional($laboratory->address->province)->name }}"
                                    data-city_id="{{ optional($laboratory->address->city)->city_id }}"
                                    data-city_name="{{ optional($laboratory->address->city)->name }}"
                                    data-barangay_id="{{ optional($laboratory->address->barangay)->barangay_id }}"
                                    data-barangay_name="{{ optional($laboratory->address->barangay)->name }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-outline-danger btn-sm delete-laboratory-btn"
                                    data-id="{{ $laboratory->account_id }}" onclick="event.stopPropagation();">
                                    <i class="bi bi-trash"></i>
                                </button> --}}
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
        document.querySelectorAll('.delete-laboratory-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const laboratoryId = this.dataset.id;
                document.getElementById('delete_laboratory_id').value = laboratoryId;

                const deleteModalEl = document.getElementById('delete-laboratory-modal');
                const deleteModal = new bootstrap.Modal(deleteModalEl);
                deleteModal.show();
            });
        });
    });
</script>
{{-- @include('pages.laboratories.modals.info')
@include('pages.laboratories.modals.edit')
@include('pages.laboratories.modals.delete') --}}

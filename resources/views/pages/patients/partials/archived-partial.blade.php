<div class="card-body">
    @if($patients->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="text-muted mt-3">No archived patients found.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr class="table-warning">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($patient->profile_picture)
                                        <img src="{{ asset('storage/' . $patient->profile_picture) }}" 
                                             alt="{{ $patient->full_name }}" 
                                             class="rounded-circle"
                                             style="width: 32px; height: 32px; object-fit: cover; opacity: 0.7;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                             style="width: 32px; height: 32px; opacity: 0.7;">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                    @endif
                                    <span>{{ $patient->full_name }}</span>
                                </div>
                            </td>
                            <td>{{ $patient->email ?? 'N/A' }}</td>
                            <td>{{ $patient->mobile_no ?? 'N/A' }}</td>
                            <td>{{ $patient->sex }}</td>
                            <td>{{ $patient->age ?? 'N/A' }}</td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#unarchive-patient-modal"
                                        data-patient-id="{{ $patient->patient_id }}"
                                        data-patient-name="{{ $patient->full_name }}"
                                        title="Restore Patient">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $patients->appends(['filter_by_account' => request('filter_by_account')])->links() }}
        </div>
    @endif
</div>
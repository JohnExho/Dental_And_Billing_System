{{-- resources/views/pages/clinics/partials/index-partial.blade.php --}}

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <a href="#" class="btn btn-light btn-sm float-end"
           data-bs-toggle="modal" 
           data-bs-target="#add-clinic-modal">
            <i class="bi bi-plus-circle"></i> Add Clinic
        </a>
    </div>
    <div class="card-body p-0">
        @if($clinics->isEmpty())
            <p class="p-3 mb-0 text-danger text-center">No clinics found. Add one using the button above.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Schedule</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clinics as $clinic)
                        <tr>
                            <td class="fw-semibold">{{ $clinic->name }}</td>
                            <td>{{ Str::limit($clinic->description, 50) ?? 'No Description Given' }}</td>
                         <td>
    @php
        $firstSummary = optional($clinic->clinicSchedules->first())->schedule_summary;
        $weekDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $schedules = $clinic->clinicSchedules->keyBy('day_of_week');
        $collapseId = 'clinic-schedule-' . $clinic->clinic_id;
    @endphp

    <!-- Show first summary -->
    <span>{{ $firstSummary ?? 'N/A' }}</span>

    @if($clinic->clinicSchedules->count() > 1)
        <!-- Toggle button -->
        <button class="btn btn-sm btn-link p-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
            (view all)
        </button>

        <!-- Collapsible full schedule -->
        <div class="collapse mt-1" id="{{ $collapseId }}">
            <ul class="list-unstyled mb-0">
                @foreach($weekDays as $day)
                    @php $sched = $schedules[$day] ?? null; @endphp
                    <li>
                        <strong>{{ $day }}:</strong>
                        @if($sched)
                            {{ $sched->start_time }} - {{ $sched->end_time }}
                        @else
                            N/A
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</td>

                            <td>
                                {{ optional($clinic->address)->house_no }} {{ optional($clinic->address)->street }}<br>
                                {{ optional($clinic->address->barangay)->name ?? '' }} 
                                {{ optional($clinic->address->city)->name ?? '' }} 
                                {{ optional($clinic->address->province)->name ?? '' }}
                            </td>
                            <td>{{ $clinic->email }}</td>
                            <td>{{ $clinic->contact_no }}</td>
                            <td class="text-end">
                                <a href="#" class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#edit-clinic-modal">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{-- route('clinics.destroy', $clinic) --}}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

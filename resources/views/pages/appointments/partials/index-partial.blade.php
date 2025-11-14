<div class="container-fluid col-md-12">
    <div class="row">
        <!-- Calendar Main Area -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <!-- Header with Navigation -->
                <div class="card-header bg-info border-bottom d-flex justify-content-between align-items-center py-3">
                    @php
                        $queryParams = array_merge(request()->except(['month', 'year', 'view']), [
                            'year' => $currentYear,
                            'month' => $currentMonth,
                            'view' => $viewMode,
                        ]);
                    @endphp

                    <h4 class="mb-0 fw-bold">Calendar</h4>


                    <h4 class="mb-0 fw-bold">{{ $monthName }}</h4>

                    <div class="d-flex gap-2">

                        <a href="{{ route('appointments', array_merge($queryParams, ['month' => $prevMonth->month, 'year' => $prevMonth->year])) }}"
                            class="btn btn-outline-primary btn-sm">Previous</a>

                        <a href="{{ route('appointments', array_merge($queryParams, ['month' => $today->month, 'year' => $today->year])) }}"
                            class="btn btn-outline-primary btn-sm">Today</a>

                        <a href="{{ route('appointments', array_merge($queryParams, ['month' => $nextMonth->month, 'year' => $nextMonth->year])) }}"
                            class="btn btn-outline-primary btn-sm">Next</a>
                    </div>

                </div>

                <!-- Calendar Grid -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" style="table-layout: fixed;">
                            <thead class="bg-light">
                                <tr>
                                    @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <th class="text-center fw-semibold py-3" style="width: 14.28%;">
                                            {{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for ($week = 0; $week < 6; $week++)
                                    <tr>
                                        @for ($dow = 0; $dow < 7; $dow++)
                                            @php
                                                $cellDate = $week * 7 + $dow - $startDayOfWeek + 1;
                                                $isToday =
                                                    $cellDate == $today->day &&
                                                    $currentMonth == $today->month &&
                                                    $currentYear == $today->year;
                                                $dayEvents = $events[$cellDate] ?? [];
                                            @endphp

                                            @if ($cellDate < 1 || $cellDate > $daysInMonth)
                                                <td class="bg-light" style="height: 70px;"></td>
                                            @else
                                                <td class="{{ $isToday ? 'bg-success bg-opacity-10' : '' }} position-relative"
                                                    style="height: 120px; vertical-align: top;">
                                                    <div class="p-2">
                                                        <div
                                                            class="d-flex justify-content-between align-items-start mb-2">
                                                            <span
                                                                class="badge {{ $isToday ? 'bg-success' : 'bg-light text-dark' }} rounded-circle"
                                                                style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                                {{ $cellDate }}
                                                            </span>
                                                        </div>

                                                        @if (count($dayEvents))
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($dayEvents as $event)
                                                                    @php
                                                                        $eventType = $eventTypes[$event['type']] ?? [
                                                                            'icon' => 'bi-question',
                                                                            'label' => 'Unknown',
                                                                        ];
                                                                        $color = $event['color'] ?? '#6c757d'; // fallback gray
                                                                    @endphp
                                                                    <a href="{{ $event['url'] }}"
                                                                        class="text-decoration-none">
                                                                        <span
                                                                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center"
                                                                            style="width: 24px; height: 24px; font-size: 0.7rem; background-color: {{ $color }};"
                                                                            title="{{ $eventType['label'] }}: {{ $event['text'] }}"
                                                                            data-bs-toggle="tooltip">
                                                                            <i class="bi {{ $eventType['icon'] }}"></i>
                                                                        </span>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @endif




                                                    </div>
                                                </td>
                                            @endif
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush

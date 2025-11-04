@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    use Illuminate\Support\Collection;

    // Get current or navigated month/year from query
    $currentYear = request('year', now()->year);
    $currentMonth = request('month', now()->month);

    // Set up Carbon dates
    $firstDayOfMonth = Carbon::create($currentYear, $currentMonth, 1);
    $monthName = $firstDayOfMonth->format('F Y');
    $daysInMonth = $firstDayOfMonth->daysInMonth;
    $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 = Sunday, 6 = Saturday

    // Prev/Next months for navigation
    $prevMonth = $firstDayOfMonth->copy()->subMonth();
    $nextMonth = $firstDayOfMonth->copy()->addMonth();

    // Safely convert holidayEvents to a collection (even if null or array)
    $holidayEvents = collect($holidayEvents ?? []);

    // Example internal events (manual for now)
    $customEvents = collect([
        10 => ['🎂 Dr. Ramos Birthday'],
        18 => ['🎂 Assistant Lea Birthday'],
        5 => ['🏠 Consultation - John Doe'],
        7 => ['🏠 Follow-up - Alex Tan'],
    ]);

    // Merge API + custom events
    $events = $holidayEvents->union($customEvents);
@endphp

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <a href="?month={{ $prevMonth->month }}&year={{ $prevMonth->year }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left-circle"></i> Prev
    </a>

    <h4 class="mb-0 fw-semibold text-center flex-grow-1"><span class="fi fi-ph"></span> {{ $monthName }}</h4>

    <a href="?month={{ $nextMonth->month }}&year={{ $nextMonth->year }}" class="btn btn-light btn-sm">
        Next <i class="bi bi-arrow-right-circle"></i>
    </a>
</div>
<div class="card-body bg-light">
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Sun</th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                </tr>
            </thead>
            <tbody>
                @for ($week = 0; $week < 6; $week++)
                    <tr>
                        @for ($dow = 0; $dow < 7; $dow++)
                            @php
                                $cellDate = $week * 7 + $dow - $startDayOfWeek + 1;
                                $isToday =
                                    $cellDate == now()->day &&
                                    $currentMonth == now()->month &&
                                    $currentYear == now()->year;
                            @endphp

                            @if ($cellDate < 1 || $cellDate > $daysInMonth)
                                <td class="bg-white"></td>
                            @else
                                <td class="{{ $isToday ? 'bg-success text-white fw-bold' : 'bg-white' }}">
                                    <div>{{ $cellDate }}</div>

                                    @if (isset($events[$cellDate]))
                                        <div class="mt-2">
                                            @foreach ($events[$cellDate] as $event)
                                                <div
                                                    class="badge 
                                                            @if (Str::contains($event, '🎂')) bg-danger 
                                                            @elseif(Str::contains($event, '🏠')) bg-primary 
                                                            @else bg-warning text-dark @endif
                                                            mb-1 w-100">
                                                    {{ $event }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            @endif
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

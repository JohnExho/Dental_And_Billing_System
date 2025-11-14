<?php

namespace App\Http\Controllers;

use App\Models\Recall;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = $request->input('year', now()->year);
        $currentMonth = $request->input('month', now()->month);
        $viewMode = $request->input('view', 'month');

        $firstDayOfMonth = Carbon::create($currentYear, $currentMonth, 1);
        $monthName = $firstDayOfMonth->format('F Y');
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        $startDayOfWeek = $firstDayOfMonth->dayOfWeek;

        $prevMonth = $firstDayOfMonth->copy()->subMonth();
        $nextMonth = $firstDayOfMonth->copy()->addMonth();
        $today = now();

        $holidayEvents = collect([]);

        $eventTypes = [
            'appointment' => ['icon' => 'bi-calendar-check', 'color' => 'primary', 'label' => 'Appointments'],
            'followup' => ['icon' => 'bi-arrow-repeat', 'color' => 'info', 'label' => 'Follow-Ups/Recalls'],
        ];

        // Get filters from request
        $associatesFilter = $request->input('associates', []); // array of associate_ids
        $showAppointments = $request->has('show_appointments') || ! $request->hasAny(['show_appointments', 'show_followups']);
        $showFollowups = $request->has('show_followups') || ! $request->hasAny(['show_appointments', 'show_followups']);

        $associates = \App\Models\Associate::all(); // Or however you fetch them
        $associateColors = $associates->pluck('color', 'associate_id')->toArray();

        $events = [];

        if ($showFollowups) {
            $recallsQuery = Recall::with('patient')
                ->whereYear('recall_date', $currentYear)
                ->whereMonth('recall_date', $currentMonth)
                ->where('status', 'pending');

            // Only filter by selected associates if any are chosen
            if (! empty($associatesFilter)) {
                $recallsQuery->whereIn('associate_id', $associatesFilter);
            }

            $recalls = $recallsQuery->get();

            foreach ($recalls as $recall) {
                if (! $recall->recall_date || ! $recall->patient) {
                    continue;
                }

                $day = Carbon::parse($recall->recall_date)->day;

                // Assign color: use associate color if exists, red if no associate
                $color = $recall->associate_id
                            ? ($associateColors[$recall->associate_id] ?? '#6c757d')
                            : '#dc3545';

                $events[$day][] = [
                    'text' => $recall->patient->full_name.' - '.$recall->recall_reason,
                    'type' => 'followup',
                    'url' => route('specific-patient', ['patient_id' => $recall->patient_id]).'&tab=recalls',
                    'color' => $color,
                    'associate_id' => $recall->associate_id,
                ];
            }

        }

        $events = collect($events);

        return view('pages.appointments.index', [
            'monthName' => $monthName,
            'daysInMonth' => $daysInMonth,
            'startDayOfWeek' => $startDayOfWeek,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'today' => $today,
            'eventTypes' => $eventTypes,
            'events' => $events,
            'holidayEvents' => $holidayEvents,
            'viewMode' => $viewMode,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'associatesFilter' => $associatesFilter,
            'showFollowups' => $showFollowups,
            'showAppointments' => $showAppointments,
        ]);
    }
}

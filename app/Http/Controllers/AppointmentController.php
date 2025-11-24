<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Recall;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

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
                    'url' => route('specific-patient', ['patient_id' => $recall->patient_id, 'tab' => 'recalls']),
                    'color' => $color,
                    'associate_id' => $recall->associate_id,
                ];
            }

        }

        if ($showAppointments) {
            $appointmentsQuery = Appointment::with(['patient', 'associate'])
                ->whereYear('appointment_date', $currentYear)
                ->whereMonth('appointment_date', $currentMonth)
                ->where('status', 'scheduled');

            // Filter by selected associates if user checked any
            if (! empty($associatesFilter)) {
                $appointmentsQuery->whereIn('associate_id', $associatesFilter);
            }

            $appointments = $appointmentsQuery->get();

            foreach ($appointments as $appointment) {
                if (! $appointment->appointment_date || ! $appointment->patient) {
                    continue;
                }

                $day = Carbon::parse($appointment->appointment_date)->day;

                // Use associate color if available; gray fallback
                $color = $appointment->associate_id
                            ? ($associateColors[$appointment->associate_id] ?? '#0d6efd')
                            : '#6c757d';

                $events[$day][] = [
                    'text' => $appointment->patient->full_name.' - Appointment',
                    'type' => 'appointment',
                    'url' => route('appointments', [
                        'tab' => 'appointments',
                    ]),
                    'color' => $color,
                    'associate_id' => $appointment->associate_id,
                ];
            }
        }

        $events = collect($events);

        $appointmentsListQuery = Appointment::with(['patient', 'associate', 'account'])
            ->whereYear('appointment_date', $currentYear)
            ->whereMonth('appointment_date', $currentMonth)
            ->when(! empty($associatesFilter), fn ($q) => $q->whereIn('associate_id', $associatesFilter))
            ->orderBy('appointment_date', 'asc');

        // Example: return paginated list for the side/list partial:
        $appointments = $appointmentsListQuery->paginate(12)->withQueryString();

        return view('pages.appointments.index', [
            'appointments' => $appointments,
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

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|uuid|exists:patients,patient_id',
            'appointment_date' => 'required|date',
            'associate_id' => 'nullable|uuid|exists:associates,associate_id',
        ], );

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();

        return DB::transaction(function () use ($validated, $request) {
            $authAccount = Auth::guard('account')->user();

            // Create appointment
            $appointment = Appointment::create([
                'appointment_id' => Str::uuid(),
                'account_id' => $authAccount->account_id,
                'patient_id' => $validated['patient_id'],
                'associate_id' => $validated['associate_id'] ?? null,
                'appointment_date' => $validated['appointment_date'],
                'clinic_id' => session('clinic_id') ?? $authAccount->clinic_id ?? null,
            ]);

            // Optional: log action
            LogService::record(
                $authAccount,
                $appointment,
                'create',
                'appointment',
                'User created a new appointment',
                'Appointment ID: '.$appointment->appointment_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->back()->with('success', 'Appointment added successfully.');
        });
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|uuid|exists:appointments,appointment_id',
            'status' => 'required|string',
            'appointment_date' => 'required|date',
            'associate_id' => 'nullable|uuid|exists:associates,associate_id',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $validated = $validator->validated();
        $appointment = Appointment::find($validated['appointment_id']);

        $appointment->update([
            'status' => $validated['status'],
            'appointment_date' => $validated['appointment_date'],
            'associate_id' => $validated['associate_id'] ?? null,
        ]);

        $authAccount = Auth::guard('account')->user();

        LogService::record(
            $authAccount,
            $appointment,
            'update',
            'appointment',
            'User updated an appointment',
            'Appointment ID: '.$appointment->appointment_id,
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,appointment_id',
            'password' => 'required',
        ]);

        $deletor = Auth::guard('account')->user();
        $appointment = Appointment::findOrFail($request->appointment_id);

        // Check if the password matches the current user's password
        if (! Hash::check($request->password, $deletor->password)) {
            return back()->with('error', 'The password is incorrect.');
        }

        return DB::transaction(function () use ($appointment, $request, $deletor) {

            // Delete patient record
            $appointment->delete();

            // Logging
            LogService::record(
                $deletor,
                $appointment,
                'delete',
                'appointment',
                'User deleted an appointment',
                'appointment ID: '.$appointment->appointment_id,
                $request->ip(),
                $request->userAgent()
            );

            return redirect()->route('appointments')->with('success', 'Patient Appointment deleted successfully.');
        });
    }
}

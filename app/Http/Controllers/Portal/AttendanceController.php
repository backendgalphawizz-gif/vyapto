<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $registeredAt = Carbon::parse($user->created_at);
        $minYear = (int) $registeredAt->year;
        $minMonth = (int) $registeredAt->month;
        $maxYear = (int) now()->year;
        $maxMonth = (int) now()->month;

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $tab = $request->input('tab', 'entries') === 'holiday' ? 'holiday' : 'entries';

        $requestedMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $earliestMonth = Carbon::create($minYear, $minMonth, 1)->startOfMonth();
        $latestMonth = now()->startOfMonth();

        if ($requestedMonth->lt($earliestMonth)) {
            $month = $minMonth;
            $year = $minYear;
        } elseif ($requestedMonth->gt($latestMonth)) {
            $month = $maxMonth;
            $year = $maxYear;
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
        $today = now()->startOfDay();

        $records = Attendance::where('employee_id', $user->id)
            ->whereBetween('punch_in_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('punch_in_date')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->punch_in_date)->toDateString());

        $companyStartTime = Setting::where('type', 'company_start_time')->value('value');
        $companyEndTime = Setting::where('type', 'company_end_time')->value('value');

        $holidayMap = Holiday::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn ($holiday) => Carbon::parse($holiday->date)->toDateString());

        $stats = [
            'present' => 0,
            'holiday' => 0,
            'half_day' => 0,
            'absent' => 0,
            'late' => 0,
            'early_going' => 0,
        ];

        $calendar = [];
        $presentStatuses = ['Present', 'Late', 'Early Going', 'Half Day', 'Late Coming, Early Going'];

        for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
            $key = $date->toDateString();
            $attendance = $records->get($key);
            $isFuture = $date->gt($today);
            $isWeekend = $date->isWeekend();
            $holiday = $holidayMap->get($key);
            $status = 'Absent';
            $statusKey = 'absent';

            if ($holiday && ! ($attendance && $attendance->punch_in_time)) {
                $status = 'Holiday';
                $statusKey = 'holiday';
                if ($date->lte($today)) {
                    $stats['holiday']++;
                }
            } elseif ($isWeekend && ! ($attendance && $attendance->punch_in_time)) {
                $status = 'Weekend';
                $statusKey = 'weekend';
            } elseif ($attendance && $attendance->punch_in_time) {
                $status = $this->resolveDayStatus($attendance, $companyStartTime, $companyEndTime);
                $statusKey = $this->statusKey($status);

                if ($date->lte($today)) {
                    $stats['present']++;

                    if ($status === 'Late' || $status === 'Late Coming, Early Going') {
                        $stats['late']++;
                    }
                    if ($status === 'Half Day') {
                        $stats['half_day']++;
                    }
                    if ($status === 'Early Going' || $status === 'Late Coming, Early Going') {
                        $stats['early_going']++;
                    }
                }
            } elseif ($isFuture) {
                $status = 'Upcoming';
                $statusKey = 'future';
            } elseif ($date->lte($today) && ! $isWeekend) {
                $stats['absent']++;
            }

            if ($isFuture) {
                $statusKey = 'future';
            }

            $calendar[] = [
                'date' => $key,
                'day_name' => $date->format('D'),
                'day_number' => $date->format('j'),
                'display_date' => $date->format('d M Y'),
                'status' => $status,
                'status_key' => $statusKey,
                'punch_in' => optional($attendance?->punch_in_time)->format('h:i A'),
                'punch_out' => optional($attendance?->punch_out_time)->format('h:i A'),
                'holiday_name' => $holiday?->name,
                'is_future' => $isFuture,
                'is_weekend' => $isWeekend,
                'is_today' => $date->isToday(),
            ];
        }

        $selectedDate = $request->input('date');
        $selectedCarbon = $selectedDate ? Carbon::parse($selectedDate) : null;

        if (! $selectedCarbon || $selectedCarbon->month !== $month || $selectedCarbon->year !== $year) {
            $selectedCarbon = now()->month === $month && now()->year === $year
                ? now()->startOfDay()
                : $startOfMonth->copy();
        }

        $selectedDay = collect($calendar)->firstWhere('date', $selectedCarbon->toDateString())
            ?? $calendar[0];

        $holidayList = $holidayMap->map(function ($holiday) {
            return [
                'name' => $holiday->name,
                'date' => Carbon::parse($holiday->date)->format('d M Y'),
                'raw_date' => Carbon::parse($holiday->date)->toDateString(),
                'is_optional' => (bool) ($holiday->is_optional ?? false),
            ];
        })->sortBy('raw_date')->values();

        $monthLabel = Carbon::create($year, $month, 1)->format('F Y');

        $monthOptions = [];
        $optionCursor = Carbon::create($minYear, $minMonth, 1)->startOfMonth();
        $optionEnd = Carbon::create($maxYear, $maxMonth, 1)->startOfMonth();
        while ($optionCursor <= $optionEnd) {
            $monthOptions[] = [
                'month' => $optionCursor->month,
                'year' => $optionCursor->year,
                'label' => $optionCursor->format('F Y'),
                'selected' => $optionCursor->month === $month && $optionCursor->year === $year,
            ];
            $optionCursor->addMonth();
        }
        $monthOptions = array_reverse($monthOptions);

        $monthEntries = collect($calendar)
            ->filter(fn ($day) => ! $day['is_future'])
            ->reverse()
            ->values()
            ->all();

        return view('portal.attendance.index', compact(
            'calendar',
            'stats',
            'month',
            'year',
            'minYear',
            'minMonth',
            'maxYear',
            'maxMonth',
            'selectedDay',
            'selectedDate',
            'holidayList',
            'tab',
            'monthLabel',
            'monthOptions',
            'monthEntries'
        ));
    }

    private function resolveDayStatus($attendance, ?string $startTime, ?string $endTime): string
    {
        $punchIn = Carbon::parse($attendance->punch_in_time);
        $punchOut = $attendance->punch_out_time ? Carbon::parse($attendance->punch_out_time) : null;

        $isLateComing = false;
        if ($startTime) {
            $lateThreshold = Carbon::parse($punchIn->toDateString().' '.$startTime)->addMinutes(15);
            $isLateComing = $punchIn->gt($lateThreshold);
        }

        $isHalfDay = false;
        $isEarlyGoing = false;

        if ($punchOut && $endTime) {
            $halfDayThreshold = Carbon::parse($punchOut->toDateString().' '.$endTime)->subMinutes(90);
            $earlyGoingThreshold = Carbon::parse($punchOut->toDateString().' '.$endTime)->subMinutes(60);

            if ($punchOut->lte($halfDayThreshold)) {
                $isHalfDay = true;
            } elseif ($punchOut->lte($earlyGoingThreshold)) {
                $isEarlyGoing = true;
            }
        }

        if ($isHalfDay) {
            return 'Half Day';
        }

        if ($isLateComing && $isEarlyGoing) {
            return 'Late Coming, Early Going';
        }

        if ($isLateComing) {
            return 'Late';
        }

        if ($isEarlyGoing) {
            return 'Early Going';
        }

        return 'Present';
    }

    private function statusKey(string $status): string
    {
        return match ($status) {
            'Late', 'Late Coming, Early Going' => 'late',
            'Half Day' => 'half_day',
            'Early Going' => 'early_going',
            'Present' => 'present',
            default => 'present',
        };
    }
}

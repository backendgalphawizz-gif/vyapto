<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Api\UserToken;
use App\Models\Holiday;
use App\Services\AttendanceScheduleService;
use App\Support\StorageAssets;

class UserController extends Controller
{
	
	public function getAttendence(Request $request)
	{
		$token = str_replace('Bearer ', '', $request->header('Authorization'));
		if (!$token || !UserToken::where('token', $token)->exists()) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid or expired token'
			], 401);
		}

		$user = auth('api')->user();

		if (!$request->month || !$request->year) {
			return response()->json([
				'status' => false,
				'message' => 'Month and Year are required'
			], 422);
		}

		$startOfMonth = Carbon::create($request->year, $request->month, 1)->startOfMonth();
		$endOfMonth   = Carbon::create($request->year, $request->month, 1)->endOfMonth();

		$employee = User::with(['attendance' => function ($query) use ($startOfMonth, $endOfMonth) {
			$query->whereBetween('punch_in_time', [$startOfMonth, $endOfMonth]);
		}])->where('status', 1)->findOrFail($user->id);

		$calendar = [];
		$dateCursor = $startOfMonth->copy();

		while ($dateCursor <= $endOfMonth) {
			$calendar[$dateCursor->toDateString()] = [
				'title' => '',
				'date'  => $dateCursor->toDateString(),
				'value' => 'Absent'
			];
			$dateCursor->addDay();
		}

		$lateCount       = 0;
		$lateComingCount = 0;
		$earlyGoingCount = 0;
		$halfDayCount    = 0;
		$checkinCheckouts = [];

		$scheduleService = app(AttendanceScheduleService::class);
		$assignmentsByUser = $scheduleService->loadAssignmentsForEmployees([(int) $employee->id]);

		// ----- Process Attendance -----
		foreach ($employee->attendance as $attendance) {
			if (empty($attendance->punch_in_time)) {
				continue;
			}

			$punchIn  = Carbon::parse($attendance->punch_in_time);
			$punchOut = $attendance->punch_out_time
							? Carbon::parse($attendance->punch_out_time)
							: null;

			$date = $punchIn->toDateString();

			// Only process dates that belong to the requested month calendar.
			// This prevents creating extra keys and keeps totalPresent aligned with employeeAttendence list.
			if (!isset($calendar[$date])) {
				continue;
			}

			// If employee has punched in, day cannot be absent.
			$calendar[$date]['value'] = 'Present';

			$schedule = $scheduleService->resolveSchedule($employee, $date, $assignmentsByUser);
			$dayStatus = $scheduleService->resolveDayStatusLabel($attendance, $schedule);

			$isLateComing = in_array($dayStatus, ['Late', 'Late Coming, Early Going'], true);
			$isEarlyGoing = in_array($dayStatus, ['Early Going', 'Late Coming, Early Going'], true);
			$isHalfDay = $dayStatus === 'Half Day';

			if ($isHalfDay) {
				$halfDayCount++;
			} elseif ($isLateComing && $isEarlyGoing) {
				$lateComingCount++;
				$lateCount++;
				$earlyGoingCount++;
			} elseif ($isLateComing) {
				$lateComingCount++;
				$lateCount++;
			} elseif ($isEarlyGoing) {
				$earlyGoingCount++;
			}

			$calendar[$date]['value'] = $dayStatus;

			// ----- Build check-in/out record with raw late/early flags -----
			$checkinCheckouts[] = [
				'date'               => $punchIn->toDateString(),
				'check_in'           => $attendance->punch_in_time,
				'check_out'          => $attendance->punch_out_time,
				'check_in_location'  => $attendance->punch_in_location,
				'check_out_location' => $attendance->punch_out_location,
				'check_in_image'     => StorageAssets::publicUrl($attendance->punch_in_image),
				'check_out_image'    => StorageAssets::publicUrl($attendance->punch_out_image),
				'late_coming'        => $isLateComing,
				'early_going'        => $isEarlyGoing,
			];
		}

		// ----- Holidays -----
		$holidays = Holiday::whereBetween('date', [
			$startOfMonth->toDateString(),
			$endOfMonth->toDateString()
		])->get();

		foreach ($holidays as $holiday) {
			$day = Carbon::parse($holiday->date)->toDateString();

			if (isset($calendar[$day]) && $calendar[$day]['value'] === 'Absent') {
				$calendar[$day]['value'] = 'Holiday';
				$calendar[$day]['title'] = $holiday->name;
			}
		}

		// ----- Totals (only days on or before today — ignore future dates in the month) -----
		$today = now()->startOfDay();

		$presentStatuses = [
			'Present',
			'Late',
			'Early Going',
			'Half Day',
		];
		$totalPresent  = collect($calendar)->whereIn('value', $presentStatuses)->count();
		$totalAbsent   = collect($calendar)
			->filter(function ($row) use ($today) {
				return Carbon::parse($row['date'])->lte($today);
			})
			->where('value', 'Absent')
			->count();
		$totalHolidays = collect($calendar)->where('value', 'Holiday')->count();

		return response()->json([
			'status' => true,
			'analytics' => [],
			'data' => [
				'employeeAttendence' => array_values($calendar),

				'totalPresent'   => $totalPresent,
				'late_day_count' => $lateCount,
				'late_coming_count' => $lateComingCount,
				'early_going_count' => $earlyGoingCount,
				'half_day_count'    => $halfDayCount,
				'totalAbsent'       => (string)$totalAbsent,
				'totalHolidays'     => (string)$totalHolidays,

				'month' => $request->month,
				'year'  => $request->year,

				'checkinCheckouts' => $checkinCheckouts,
				'totalWorkingDays' => 26,

				'holidays' => $holidays->map(function ($holiday) {
					return [
						'name'        => $holiday->name,
						'date'        => $holiday->date->toDateString(),
						'is_optional' => $holiday->is_optional
					];
				}),
			]
		]);
	}


    /*public function getAttendence(Request $request)
    {
        // ----- Validate Token -----
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        if (!$token || !UserToken::where('token', $token)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }

        $user = auth('api')->user();

        // ----- Month Range -----
        $startOfMonth = Carbon::create($request->year, $request->month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($request->year, $request->month, 1)->endOfMonth();

        // ----- Load User With Needed Relations -----
        $employee = User::with([
            'attendance',
        ])
            ->where('status', 1)
            ->findOrFail($user->id);

        // ----- Prepare Default Calendar -----
        $calendar = [];
        $dateCursor = $startOfMonth->copy();
        while ($dateCursor <= $endOfMonth) {
            $calendar[$dateCursor->format('d')] = [
                'title' => '',
                'date'  => $dateCursor->toDateString(),
                'value' => 'Absent'
            ];
            $dateCursor->addDay();
        }

        // ----- Attendance -----
        foreach ($employee->attendance as $attendance) {
            $date = Carbon::parse($attendance->punch_in_time)->format('d');

            if ($attendance->half_day === 'yes') {
                $calendar[$date]['value'] = 'Half Day';
            } else {
                $calendar[$date]['value'] = 'Present';
            }
        }

        // ----- Holidays -----
        $holidays = Holiday::whereBetween('date', [
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString()
        ])->get();

        foreach ($holidays as $holiday) {
            $day = Carbon::parse($holiday->date)->format('d');

            // Do not override Present / Half Day
            if (isset($calendar[$day]) && $calendar[$day]['value'] === 'Absent') {
                $calendar[$day]['value'] = 'Holiday';
                $calendar[$day]['title'] = $holiday->name;
            }
        }

        // ----- Check-in / Check-out List -----
        $checkinCheckouts = $employee->attendance
            ->map(function ($attendance) {
                return [
                    'date' => Carbon::parse($attendance->punch_in_time)->toDateString(),
                    'check_in' => $attendance->punch_in_time,
                    'check_out' => $attendance->punch_out_time,
                    'check_in_location' => $attendance->punch_in_location,
                    'check_out_location' => $attendance->punch_out_location,
                    'check_in_image' => $attendance->punch_in_image,
                    'check_out_image' => $attendance->punch_out_image,
                ];
            })
            ->values();

        // ----- Totals (absent: only up to today) -----
        $today = now()->startOfDay();
        $totalAbsent   = collect($calendar)
            ->filter(function ($row) use ($today) {
                return Carbon::parse($row['date'])->lte($today);
            })
            ->where('value', 'Absent')
            ->count();
        $totalHolidays = collect($calendar)->where('value', 'Holiday')->count();

        return response()->json([
            'status' => true,
            'analytics' => [],
            'data' => [
                'employeeAttendence' => array_values($calendar),

                
                'totalPresent' => collect($calendar)->where('value', 'Present')->count(),
                'late_day_count' => 0,
                'half_day_count' => collect($calendar)->where('value', 'Half Day')->count(),
                'totalAbsent' => (string)$totalAbsent,
                'totalHolidays' => (string)$totalHolidays,
                'month' => $request->month,
                'year' => $request->year,
                'checkinCheckouts' => $checkinCheckouts,
                'holidays' => $holidays->map(function ($holiday) {
                    return [
                        'name' => $holiday->name,
                        'date' => $holiday->date->toDateString(),
                        'is_optional' => $holiday->is_optional
                    ];
                }),
            ]
        ]);
    }*/
}

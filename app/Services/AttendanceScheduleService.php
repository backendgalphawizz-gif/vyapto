<?php

namespace App\Services;

use App\Models\AssignmentParcel;
use App\Models\Hub;
use App\Models\Office;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AttendanceScheduleService
{
    private ?array $companyDefaults = null;

    /**
     * Prefetch assignments for a set of employees (avoids N+1 on report pages).
     *
     * @param  array<int>  $employeeIds
     */
    public function loadAssignmentsForEmployees(array $employeeIds): Collection
    {
        if ($employeeIds === [] || ! Schema::hasTable('assignment_parcel')) {
            return collect();
        }

        return AssignmentParcel::query()
            ->with(['office', 'hub'])
            ->whereIn('user_id', $employeeIds)
            ->where(function ($q) {
                $q->whereNotNull('office_id')->orWhereNotNull('hub_id');
            })
            ->orderByDesc('assignment_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('user_id');
    }

    /**
     * @return array{start_time:?string,end_time:?string,half_time:?string,source:?string,source_name:?string}
     */
    public function resolveSchedule(User $employee, string $date, ?Collection $assignmentsByUser = null): array
    {
        $company = $this->companyDefaults();
        $schedule = [
            'start_time' => $company['start_time'],
            'end_time' => $company['end_time'],
            'half_time' => $company['half_time'],
            'source' => ($company['start_time'] || $company['end_time']) ? 'company' : null,
            'source_name' => ($company['start_time'] || $company['end_time']) ? 'Company settings' : null,
        ];

        $assignment = $this->findAssignmentForDate($employee->id, $date, $assignmentsByUser);

        if ($assignment?->office_id) {
            $office = $assignment->relationLoaded('office')
                ? $assignment->office
                : Office::query()->find($assignment->office_id);

            if ($office) {
                $opening = $this->extractTime($office->opening_time);
                $closing = $this->extractTime($office->closing_time);

                if ($opening || $closing) {
                    return [
                        'start_time' => $opening ?: $company['start_time'],
                        'end_time' => $closing ?: $company['end_time'],
                        'half_time' => $company['half_time'],
                        'source' => 'office',
                        'source_name' => $office->name,
                    ];
                }
            }
        }

        if ($assignment?->hub_id) {
            $hub = $assignment->relationLoaded('hub')
                ? $assignment->hub
                : Hub::query()->find($assignment->hub_id);

            if ($hub) {
                $opening = $this->extractTime($hub->opening_time);
                $closing = $this->extractTime($hub->closing_time);

                if ($opening || $closing) {
                    return [
                        'start_time' => $opening ?: $company['start_time'],
                        'end_time' => $closing ?: $company['end_time'],
                        'half_time' => $company['half_time'],
                        'source' => 'hub',
                        'source_name' => $hub->name,
                    ];
                }
            }
        }

        // Fallback: creation-time office/hub on employee profile
        if (! empty($employee->office_id) && app(EmployeeLocationService::class)->locationActiveForDate($employee, $date)) {
            $office = Office::query()->find($employee->office_id);
            if ($office) {
                $opening = $this->extractTime($office->opening_time);
                $closing = $this->extractTime($office->closing_time);
                if ($opening || $closing) {
                    return [
                        'start_time' => $opening ?: $company['start_time'],
                        'end_time' => $closing ?: $company['end_time'],
                        'half_time' => $company['half_time'],
                        'source' => 'office',
                        'source_name' => $office->name,
                    ];
                }
            }
        }

        if (! empty($employee->hub_id)) {
            $hub = Hub::query()->find($employee->hub_id);
            if ($hub) {
                $opening = $this->extractTime($hub->opening_time);
                $closing = $this->extractTime($hub->closing_time);
                if ($opening || $closing) {
                    return [
                        'start_time' => $opening ?: $company['start_time'],
                        'end_time' => $closing ?: $company['end_time'],
                        'half_time' => $company['half_time'],
                        'source' => 'hub',
                        'source_name' => $hub->name,
                    ];
                }
            }
        }

        return $schedule;
    }

    /**
     * Evaluate attendance status + Late/Early exceptions for admin reporting.
     *
     * @param  array{start_time:?string,end_time:?string,half_time:?string}  $schedule
     * @return array{status:string,exception:string,is_late:bool,is_early:bool,is_half_day:bool}
     */
    public function evaluateDay(
        ?string $punchIn,
        ?string $punchOut,
        string $date,
        array $schedule,
        ?string $manualInException = null,
        ?string $manualOutException = null
    ): array {
        if (! $punchIn) {
            return [
                'status' => 'Absent',
                'exception' => '-',
                'is_late' => false,
                'is_early' => false,
                'is_half_day' => false,
            ];
        }

        $status = 'Present';
        $isLate = false;
        $isEarly = false;
        $isHalfDay = false;
        $parts = [];

        try {
            $pIn = Carbon::parse($punchIn);

            if (! empty($schedule['half_time'])) {
                $halfLimit = Carbon::parse($date.' '.$schedule['half_time']);
                if ($pIn->gt($halfLimit)) {
                    $isHalfDay = true;
                    $status = 'Half Day';
                }
            }

            if (! $isHalfDay && ! empty($schedule['start_time'])) {
                $startLimit = Carbon::parse($date.' '.$schedule['start_time']);
                if ($pIn->gt($startLimit)) {
                    $isLate = true;
                    $parts[] = 'Late arrival';
                }
            }
        } catch (\Throwable) {
            // Ignore unparseable punch-in; keep Present.
        }

        if ($punchOut && ! empty($schedule['end_time'])) {
            try {
                $pOut = Carbon::parse($punchOut);
                $endLimit = Carbon::parse($date.' '.$schedule['end_time']);

                if ($pOut->lt($endLimit)) {
                    $isEarly = true;
                    $parts[] = 'Early leave';
                }
            } catch (\Throwable) {
                // Ignore unparseable punch-out.
            }
        }

        foreach ([$manualInException, $manualOutException] as $manual) {
            $manual = is_string($manual) ? trim($manual) : '';
            if ($manual === '') {
                continue;
            }
            if (! collect($parts)->contains(fn ($part) => str_contains($part, $manual) || str_contains($manual, $part))) {
                $parts[] = $manual;
            }
            if (stripos($manual, 'late') !== false) {
                $isLate = true;
            }
            if (stripos($manual, 'early') !== false) {
                $isEarly = true;
            }
        }

        return [
            'status' => $status,
            'exception' => $parts === [] ? '-' : implode(' ', $parts),
            'is_late' => $isLate,
            'is_early' => $isEarly,
            'is_half_day' => $isHalfDay,
        ];
    }

    /**
     * Portal/API day label (supports grace + early-leave windows).
     *
     * @param  array{start_time:?string,end_time:?string}  $schedule
     */
    public function resolveDayStatusLabel($attendance, array $schedule, int $lateGraceMinutes = 15): string
    {
        if (empty($attendance->punch_in_time)) {
            return 'Absent';
        }

        $punchIn = Carbon::parse($attendance->punch_in_time);
        $punchOut = $attendance->punch_out_time ? Carbon::parse($attendance->punch_out_time) : null;
        $date = $punchIn->toDateString();

        $isLateComing = false;
        if (! empty($schedule['start_time'])) {
            $lateThreshold = Carbon::parse($date.' '.$schedule['start_time'])->addMinutes($lateGraceMinutes);
            $isLateComing = $punchIn->gt($lateThreshold);
        }

        $isHalfDay = false;
        $isEarlyGoing = false;

        if ($punchOut && ! empty($schedule['end_time'])) {
            $end = Carbon::parse($date.' '.$schedule['end_time']);
            $halfDayThreshold = $end->copy()->subMinutes(90);
            $earlyGoingThreshold = $end->copy()->subMinutes(60);

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

    /**
     * @return array{start_time:?string,end_time:?string,half_time:?string}
     */
    public function companyDefaults(): array
    {
        if ($this->companyDefaults !== null) {
            return $this->companyDefaults;
        }

        $rows = Setting::query()
            ->whereIn('type', ['company_start_time', 'company_end_time', 'company_half_time'])
            ->pluck('value', 'type');

        $this->companyDefaults = [
            'start_time' => $this->normalizeTimeString($rows['company_start_time'] ?? null),
            'end_time' => $this->normalizeTimeString($rows['company_end_time'] ?? null),
            'half_time' => $this->normalizeTimeString($rows['company_half_time'] ?? null),
        ];

        return $this->companyDefaults;
    }

    private function findAssignmentForDate(int $employeeId, string $date, ?Collection $assignmentsByUser): ?AssignmentParcel
    {
        $pool = $assignmentsByUser
            ? collect($assignmentsByUser->get($employeeId, []))
            : AssignmentParcel::query()
                ->where('user_id', $employeeId)
                ->where(function ($q) {
                    $q->whereNotNull('office_id')->orWhereNotNull('hub_id');
                })
                ->orderByDesc('assignment_date')
                ->orderByDesc('id')
                ->get();

        if ($pool->isEmpty()) {
            return null;
        }

        $matching = $pool->first(function (AssignmentParcel $assignment) use ($date) {
            return $this->assignmentCoversDate($assignment, $date);
        });

        if ($matching) {
            return $matching;
        }

        // No exact-date match: use the nearest prior assignment, else latest.
        $prior = $pool
            ->filter(function (AssignmentParcel $assignment) use ($date) {
                $anchor = $this->assignmentStartDate($assignment);

                return $anchor && $anchor <= $date;
            })
            ->sortByDesc(fn (AssignmentParcel $a) => $this->assignmentStartDate($a).'|'.$a->id)
            ->first();

        return $prior ?: $pool->first();
    }

    private function assignmentCoversDate(AssignmentParcel $assignment, string $date): bool
    {
        $from = $this->assignmentStartDate($assignment);
        $to = $this->assignmentEndDate($assignment) ?: $from;

        if (! $from) {
            return false;
        }

        return $date >= $from && $date <= $to;
    }

    private function assignmentStartDate(AssignmentParcel $assignment): ?string
    {
        if ($assignment->from_date) {
            return Carbon::parse($assignment->from_date)->toDateString();
        }
        if ($assignment->assignment_date) {
            return Carbon::parse($assignment->assignment_date)->toDateString();
        }

        return null;
    }

    private function assignmentEndDate(AssignmentParcel $assignment): ?string
    {
        if ($assignment->to_date) {
            return Carbon::parse($assignment->to_date)->toDateString();
        }

        // Hub daily assignments typically only set assignment_date.
        return $this->assignmentStartDate($assignment);
    }

    private function extractTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable) {
            return $this->normalizeTimeString(is_string($value) ? $value : null);
        }
    }

    private function normalizeTimeString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}

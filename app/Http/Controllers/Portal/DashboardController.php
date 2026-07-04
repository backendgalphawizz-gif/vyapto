<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\EmployeeSalary;
use App\Models\ParcelDetail;
use App\Models\Setting;
use App\Models\UserSalary;
use App\Services\EmployeePunchService;
use App\Services\EmployeeRideService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(EmployeePunchService $punchService, EmployeeRideService $rideService)
    {
        $user = Auth::user();
        $attendance = $punchService->todayAttendance($user);

        $todayParcelsQuery = ParcelDetail::where('user_id', $user->id)
            ->whereDate('created_at', today());

        $pendingParcelsToday = (clone $todayParcelsQuery)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->count();

        $deliveredParcelsToday = (clone $todayParcelsQuery)
            ->where('status', 'delivered')
            ->count();

        $pendingParcels = $rideService->canViewShipments($user, $attendance)
            ? ParcelDetail::where('user_id', $user->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->where(function ($query) {
                    $query->whereDate('created_at', today())
                        ->orWhereIn('status', ['pending', 'assigned', 'in_transit']);
                })
                ->count()
            : 0;

        $month = now()->month;
        $year = now()->year;
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = Carbon::create($year, $month, 1)->endOfMonth();

        $monthRecords = Attendance::where('employee_id', $user->id)
            ->whereBetween('punch_in_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $presentCount = $monthRecords->filter(fn ($row) => ! empty($row->punch_in_time))->count();
        $workingDays = 0;
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            if (! $date->isWeekend()) {
                $workingDays++;
            }
        }
        $absentCount = max(0, $workingDays - $presentCount);

        $salaryRecord = EmployeeSalary::where('employee_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->first();

        $userSalary = UserSalary::where('user_id', $user->id)->first();
        $estimatedSalary = $salaryRecord?->net_salary ?? $userSalary?->salary_amount ?? 0;
        $salarySlipUrl = $salaryRecord
            ? route('portal.salary.show', optional($salaryRecord->date)->format('Y-m-01'))
            : route('portal.salary.index');

        $avatar = $user->profile_image
            ? (str_starts_with($user->profile_image, 'http') ? $user->profile_image : asset($user->profile_image))
            : asset('assets/admin/images/no-image.png');

        $rideStatus = $rideService->rideStatus($user, $attendance);
        $rideStatusLabel = $rideService->rideStatusLabel($user, $attendance);
        $canStartRide = $rideService->canStartRide($user, $attendance);
        $canEndRide = $rideService->canEndRide($user, $attendance);
        $canViewShipments = $rideService->canViewShipments($user, $attendance);
        $rideAction = $canEndRide ? 'end' : 'start';
        $suggestedVehicleNumber = $rideService->suggestedVehicleNumber($user, $rideAction);

        return view('portal.dashboard', compact(
            'user',
            'attendance',
            'pendingParcels',
            'pendingParcelsToday',
            'deliveredParcelsToday',
            'presentCount',
            'absentCount',
            'workingDays',
            'estimatedSalary',
            'salarySlipUrl',
            'avatar',
            'rideStatus',
            'rideStatusLabel',
            'canStartRide',
            'canEndRide',
            'canViewShipments',
            'rideAction',
            'suggestedVehicleNumber'
        ));
    }
}

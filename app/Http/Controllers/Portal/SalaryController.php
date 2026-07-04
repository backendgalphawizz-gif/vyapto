<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Api\AttendanceController as ApiAttendanceController;
use App\Http\Controllers\Controller;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $user = Auth::user();

        $records = EmployeeSalary::where('employee_id', $user->id)
            ->whereYear('date', $year)
            ->orderByDesc('date')
            ->get()
            ->map(function ($salary) use ($user) {
                $salary->slip_url = url('/api/salary-slip/'.$user->id.'/'.optional($salary->date)->format('Y-m-01'));
                $salary->pdf_url = url('/api/salary-slip-pdf/'.$user->id.'/'.optional($salary->date)->format('Y-m-01'));

                return $salary;
            });

        return view('portal.salary.index', compact('records', 'year'));
    }

    public function show(string $date)
    {
        $user = Auth::user();

        if ((int) request('employee_id', $user->id) !== (int) $user->id) {
            abort(403);
        }

        return app(ApiAttendanceController::class)->salarySlipView($user->id, $date);
    }
}

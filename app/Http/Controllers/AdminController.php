<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Support\StaffRoles;

class AdminController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $learnerCount = $this->tableCount('learners');
        $employeeCount = User::whereIn('role_id', StaffRoles::assignableIds())->count();

        $mailLogCount = $this->tableCount('email_logs');
        $announcementCount = $this->tableCount('announcements');
        $attendanceCount = $this->tableCount('attendance');
        $vendorCount = $this->tableCount('vendors');
        $vehicleCount = $this->tableCount('vehicles');
        $hubCount = $this->tableCount('hubs');
        $vehicleUsageCount = $this->tableCount('vehicle_usage');

        $assignmentTable = $this->firstExistingTable(['assignment_parcel', 'assignment_parcels']);
        $assignmentCount = $assignmentTable ? DB::table($assignmentTable)->count() : 0;
        $todayAssignmentCount = $assignmentTable
            ? DB::table($assignmentTable)->whereDate('created_at', now()->toDateString())->count()
            : 0;

        $todayVehicleUsageCount = $this->tableExists('vehicle_usage')
            ? DB::table('vehicle_usage')->whereDate('created_at', now()->toDateString())->count()
            : 0;

        $startDate = Carbon::today()->subDays(6);
        $labels = collect(range(0, 6))
            ->map(fn ($offset) => $startDate->copy()->addDays($offset)->format('d M'))
            ->values();

        $vehicleUsageTrend = $this->dailyTrend('vehicle_usage', $startDate);
        $assignmentTrend = $assignmentTable ? $this->dailyTrend($assignmentTable, $startDate) : collect(array_fill(0, 7, 0));

        $assignmentStatusData = collect();
        if ($assignmentTable && Schema::hasColumn($assignmentTable, 'status')) {
            $assignmentStatusData = DB::table($assignmentTable)
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($row) => [
                    'status' => $row->status ?: 'unknown',
                    'total' => (int) $row->total,
                ]);
        }

        $recentVehicleUsage = $this->tableExists('vehicle_usage')
            ? DB::table('vehicle_usage as vu')
                ->leftJoin('users as u', 'u.id', '=', 'vu.user_id')
                ->select([
                    'vu.vehicle_number',
                    'vu.user_id',
                    DB::raw('COALESCE(u.name, CONCAT("User #", vu.user_id)) as user_name'),
                    'vu.kms',
                    'vu.created_at',
                ])
                ->latest('vu.created_at')
                ->limit(8)
                ->get()
            : collect();

        $recentAssignments = collect();
        if ($assignmentTable) {
            $query = DB::table($assignmentTable)->select(['id', 'status', 'created_at']);
            if (Schema::hasColumn($assignmentTable, 'vehicle_number')) {
                $query->addSelect('vehicle_number');
            }
            if (Schema::hasColumn($assignmentTable, 'parcel_quantity')) {
                $query->addSelect('parcel_quantity');
            }
            $recentAssignments = $query->latest('created_at')->limit(8)->get();
        }

        return view('admin.dashboard', compact(
            'userCount',
            'learnerCount',
            'employeeCount',
            'mailLogCount',
            'announcementCount',
            'attendanceCount',
            'vendorCount',
            'vehicleCount',
            'hubCount',
            'vehicleUsageCount',
            'assignmentCount',
            'todayAssignmentCount',
            'todayVehicleUsageCount',
            'labels',
            'vehicleUsageTrend',
            'assignmentTrend',
            'assignmentStatusData',
            'recentVehicleUsage',
            'recentAssignments'
        ));
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function tableCount(string $table): int
    {
        return $this->tableExists($table) ? (int) DB::table($table)->count() : 0;
    }

    private function firstExistingTable(array $tables): ?string
    {
        foreach ($tables as $table) {
            if ($this->tableExists($table)) {
                return $table;
            }
        }
        return null;
    }

    private function dailyTrend(string $table, Carbon $startDate)
    {
        if (!$this->tableExists($table)) {
            return collect(array_fill(0, 7, 0));
        }

        $raw = DB::table($table)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereDate('created_at', '>=', $startDate->toDateString())
            ->groupBy('day')
            ->pluck('total', 'day');

        return collect(range(0, 6))->map(function ($offset) use ($startDate, $raw) {
            $date = $startDate->copy()->addDays($offset)->toDateString();
            return (int) ($raw[$date] ?? 0);
        })->values();
    }
}

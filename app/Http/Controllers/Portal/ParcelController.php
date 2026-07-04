<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AssignmentParcel;
use App\Models\ParcelDetail;
use App\Services\EmployeePunchService;
use App\Services\EmployeeRideService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ParcelController extends Controller
{
    public function index(
        EmployeePunchService $punchService,
        EmployeeRideService $rideService
    ) {
        $user = Auth::user();
        $attendance = $punchService->todayAttendance($user);
        $canViewShipments = $rideService->canViewShipments($user, $attendance);
        $vehicleTypeColumn = $this->vehicleTypeColumn();

        if (! $canViewShipments) {
            return view('portal.parcels.index', [
                'parcels' => collect(),
                'statuses' => AssignmentParcel::getStatuses(),
                'vehicleTypeColumn' => $vehicleTypeColumn,
                'canViewShipments' => false,
                'rideBlockedMessage' => $this->rideBlockedMessage($user, $attendance, $rideService),
            ]);
        }

        $parcels = ParcelDetail::with([
            'assignmentParcel.vendor:id,name',
            'assignmentParcel.vehicle:id,vehicle_number'.($vehicleTypeColumn ? ','.$vehicleTypeColumn : ''),
            'assignmentParcel.hub:id,name',
        ])
            ->where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereDate('created_at', today())
                    ->orWhereIn('status', ['pending', 'assigned', 'in_transit']);
            })
            ->orderByDesc('id')
            ->get();

        $statuses = AssignmentParcel::getStatuses();

        return view('portal.parcels.index', compact(
            'parcels',
            'statuses',
            'vehicleTypeColumn',
            'canViewShipments'
        ));
    }

    public function updateStatus(
        Request $request,
        EmployeePunchService $punchService,
        EmployeeRideService $rideService
    ) {
        $attendance = $punchService->todayAttendance(Auth::user());

        if (! $rideService->canViewShipments(Auth::user(), $attendance)) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Start your ride to update shipment status.');
        }

        $validated = $request->validate([
            'parcel_id' => 'required|string',
            'status' => 'required|string',
        ]);

        $allowed = array_keys(AssignmentParcel::getStatuses());
        if (! in_array($validated['status'], $allowed, true)) {
            return back()->with('error', 'Invalid parcel status.');
        }

        $parcel = ParcelDetail::where('user_id', Auth::id())
            ->where('parcel_id', $validated['parcel_id'])
            ->first();

        if (! $parcel && preg_match('/^SID-(\d+)$/', $validated['parcel_id'], $matches)) {
            $parcel = ParcelDetail::where('user_id', Auth::id())
                ->where('id', (int) $matches[1])
                ->first();
        }

        if (! $parcel) {
            return back()->with('error', 'Parcel not found.');
        }

        $parcel->update(['status' => $validated['status']]);

        return back()->with('success', 'Parcel status updated.');
    }

    private function vehicleTypeColumn(): ?string
    {
        foreach (['vehicle_type', 'type', 'model', 'vehicle_model'] as $column) {
            if (Schema::hasColumn('vehicles', $column)) {
                return $column;
            }
        }

        return null;
    }

    private function rideBlockedMessage($user, $attendance, EmployeeRideService $rideService): string
    {
        if (! $attendance || ! $attendance->punch_in_time) {
            return 'Please punch in first. After that, start your ride to view today\'s shipments.';
        }

        if ($attendance->punch_out_time) {
            return 'Your shift is completed for today.';
        }

        if (! $rideService->isRideActive($user)) {
            return 'Start your ride from the dashboard to view and update shipments.';
        }

        return 'Shipments are not available right now.';
    }
}

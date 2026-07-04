<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ParcelDetail;
use App\Models\User;
use App\Models\VehicleUsage;
use App\Services\EmployeePunchService;
use App\Services\EmployeeRideService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleUsageController extends Controller
{
    public function index()
    {
        $entries = VehicleUsage::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(15);

        return view('portal.vehicle-usage.index', compact('entries'));
    }

    public function create(EmployeePunchService $punchService, EmployeeRideService $rideService)
    {
        $user = Auth::user();
        $attendance = $punchService->todayAttendance($user);

        if (! $attendance || ! $attendance->punch_in_time) {
            return redirect()->route('portal.punch.index')
                ->with('error', 'Please punch in before starting or ending a ride.');
        }

        if ($attendance->punch_out_time) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'You have already punched out for today.');
        }

        $rideAction = $rideService->nextRideAction($user);
        $suggestedVehicleNumber = $rideService->suggestedVehicleNumber($user, $rideAction);

        return view('portal.vehicle-usage.create', compact('rideAction', 'suggestedVehicleNumber'));
    }

    public function store(
        Request $request,
        EmployeePunchService $punchService,
        EmployeeRideService $rideService
    ) {
        $user = Auth::user();
        $attendance = $punchService->todayAttendance($user);
        $today = Carbon::today()->toDateString();

        if (! $attendance || ! $attendance->punch_in_time) {
            return back()->withInput()->with('error', 'Please punch in before starting or ending a ride.');
        }

        if ($attendance->punch_out_time) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'You have already punched out for today.');
        }

        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:100',
            'kms' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $todayEntryCount = $rideService->todayUsageCount($user);
        $rideAction = $rideService->nextRideAction($user);

        if ($rideAction === 'start' && ! $rideService->canStartRide($user, $attendance)) {
            return back()->withInput()->with('error', 'You cannot start a ride right now.');
        }

        if ($rideAction === 'end' && ! $rideService->canEndRide($user, $attendance)) {
            return back()->withInput()->with('error', 'Please start a ride before ending it.');
        }

        if ($rideAction === 'end') {
            $startEntry = $rideService->currentRideStartEntry($user);
            if ($startEntry) {
                $validated['vehicle_number'] = $startEntry->vehicle_number;
            }

            $vehicleError = $rideService->validateEndRideVehicleNumber($user, $validated['vehicle_number']);
            if ($vehicleError) {
                return back()
                    ->withInput()
                    ->withErrors(['vehicle_number' => $vehicleError])
                    ->with('error', $vehicleError);
            }

            $kmError = $rideService->validateEndRideKm($user, (float) $validated['kms']);
            if ($kmError) {
                return back()
                    ->withInput()
                    ->withErrors(['kms' => $kmError])
                    ->with('error', $kmError);
            }
        } elseif (empty(trim($validated['vehicle_number']))) {
            $suggested = $rideService->suggestedVehicleNumber($user, 'start');
            if ($suggested) {
                $validated['vehicle_number'] = $suggested;
            }
        }

        $path = $request->file('image')->store('vehicle-usage', 'public');
        $shouldUpdateParcelStatus = ($todayEntryCount === 0);

        DB::beginTransaction();

        try {
            VehicleUsage::create([
                'user_id' => $user->id,
                'vehicle_number' => $validated['vehicle_number'],
                'kms' => $validated['kms'],
                'image' => $path,
            ]);

            if ($shouldUpdateParcelStatus) {
                ParcelDetail::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->whereNotIn('status', [ParcelDetail::STATUS_DELIVERED, ParcelDetail::STATUS_CANCELLED])
                    ->update(['status' => ParcelDetail::STATUS_ASSIGNED]);
            }

            $todayEntryCountAgain = $rideService->todayUsageCount($user);

            User::where('id', $user->id)->update([
                'status_count' => ($todayEntryCountAgain % 2 === 1) ? 1 : 2,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Failed to save vehicle usage.');
        }

        $message = ($todayEntryCount % 2 === 0)
            ? 'Ride started successfully.'
            : 'Ride ended successfully.';

        return redirect()->route('portal.dashboard')->with('success', $message);
    }
}

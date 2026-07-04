<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParcelDetail;
use App\Models\VehicleUsage;
use App\Services\EmployeeRideService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class VehicalController extends Controller
{
	public function storeUsage(Request $request, EmployeeRideService $rideService)
	{
		$authUser = auth('api')->user();
		$today = Carbon::today()->toDateString();

		if (!$authUser) {
			return response()->json([
				'status' => false,
				'message' => 'Unauthorized',
			], 401);
		}

		$validated = $request->validate([
			'vehicle_number' => 'required|string|max:100',
			'kms' => 'required|numeric|min:0',
			'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
			'user_id' => 'nullable|integer',
		]);

		if ($request->filled('user_id') && (int) $request->input('user_id') !== (int) $authUser->id) {
			return response()->json([
				'status' => false,
				'message' => 'You can only create usage for your own user.',
			], 403);
		}

		$todayEntryCount = $rideService->todayUsageCount($authUser);
		$rideAction = $rideService->nextRideAction($authUser);

		if ($rideAction === 'end') {
			$kmError = $rideService->validateEndRideKm($authUser, (float) $validated['kms']);
			if ($kmError) {
				return response()->json([
					'status' => false,
					'message' => $kmError,
				], 422);
			}
		}

		// if ($todayEntryCount >= 2) {
		// 	return response()->json([
		// 		'status' => false,
		// 		'message' => 'Vehicle usage can be marked maximum 2 times per day.',
		// 	], 422);
		// }

		if (!$this->parcelStatusAllowsAssigned()) {
			return response()->json([
				'status' => false,
				'message' => 'parcel_detail.status enum does not allow assigned. Please update DB enum first.',
			], 422);
		}

		$path = $request->file('image')->store('vehicle-usage', 'public');
		$statusToApply = ParcelDetail::STATUS_ASSIGNED;
		$shouldUpdateParcelStatus = ($todayEntryCount === 0);

		DB::beginTransaction();

		try {
			$usage = VehicleUsage::create([
				'user_id' => $authUser->id,
				'vehicle_number' => $validated['vehicle_number'],
				'kms' => $validated['kms'],
				'image' => $path,
			]);

			$todayEntryCountagain = $rideService->todayUsageCount($authUser);

			$updatedParcels = 0;
			if ($shouldUpdateParcelStatus) {
				$updatedParcels = ParcelDetail::query()
					->where('user_id', $authUser->id)
					->whereDate('created_at', $today)
					->whereNotIn('status', [ParcelDetail::STATUS_DELIVERED, ParcelDetail::STATUS_CANCELLED])
					->update(['status' => $statusToApply]);
			}
			// return $todayEntryCount;
			$res = User::find($authUser->id)->update([
				'status_count' => ($todayEntryCountagain % 2 == 1) ? 1 : 2
			]);
			// return $res;

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();

			return response()->json([
				'status' => false,
				'message' => 'Failed to save vehicle usage.',
				'error' => $e->getMessage(),
			], 500);
		}

		return response()->json([
			'status' => true,
			'message' => ($todayEntryCount%2 == 0) ? 'Ride start successfully' : 'Ride end successfully',
			'today_entry_count' => $todayEntryCount + 1,
			'status_update_applied' => $shouldUpdateParcelStatus,
			'updated_parcel_count' => $updatedParcels,
			'applied_parcel_status' => $statusToApply,
			'data' => [
				'id' => $usage->id,
				'user_id' => $usage->user_id,
				'vehicle_number' => $usage->vehicle_number,
				'kms' => (string) $usage->kms,
				'image' => $usage->image_url,
				'created_at' => optional($usage->created_at)->format('Y-m-d H:i:s'),
			],
		]);
	}

	public function usageList(Request $request)
	{
		$authUser = auth('api')->user();

		if (!$authUser) {
			return response()->json([
				'status' => false,
				'message' => 'Unauthorized',
			], 401);
		}

		$userId = (int) $request->input('user_id', $authUser->id);
		if ($userId !== (int) $authUser->id) {
			return response()->json([
				'status' => false,
				'message' => 'You can only view your own usage entries.',
			], 403);
		}

		$list = VehicleUsage::query()
			->where('user_id', $userId)
			->orderByDesc('id')
			->get()
			->map(function ($usage) {
				return [
					'id' => $usage->id,
					'user_id' => $usage->user_id,
					'vehicle_number' => $usage->vehicle_number,
					'kms' => (string) $usage->kms,
					'image' => $usage->image_url,
					'created_at' => optional($usage->created_at)->format('Y-m-d H:i:s'),
				];
			})
			->values();

		return response()->json([
			'status' => true,
			'user_id' => $userId,
			'total' => $list->count(),
			'data' => $list,
		]);
	}

	private function parcelStatusAllowsAssigned(): bool
	{
		try {
			$column = DB::selectOne("SHOW COLUMNS FROM `parcel_detail` LIKE 'status'");
			if (!$column) {
				return true;
			}

			$type = strtolower((string) ($column->Type ?? $column->type ?? ''));
			if ($type === '' || stripos($type, 'enum(') !== 0) {
				return true;
			}

			return strpos($type, "'assigned'") !== false;
		} catch (\Throwable $e) {
			// If schema inspection fails, keep old behavior and let DB enforce constraints.
			return true;
		}
	}
}


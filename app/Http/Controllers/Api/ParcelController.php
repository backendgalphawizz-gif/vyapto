<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParcelDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ParcelController extends Controller
{
	public function updateParcelStatus(Request $request)
	{
		$authUser = auth('api')->user();

		if (!$authUser) {
			return response()->json([
				'status' => false,
				'message' => 'Unauthorized',
			], 401);
		}

		$validated = $request->validate([
			'parcel_id' => 'required|string',
			'status' => 'required|string',
		]);

		$allowedStatuses = array_keys(ParcelDetail::getStatuses());
		if (!in_array($validated['status'], $allowedStatuses, true)) {
			return response()->json([
				'status' => false,
				'message' => 'Invalid status value.',
				'allowed_statuses' => $allowedStatuses,
			], 422);
		}

		$parcel = ParcelDetail::query()
			->where('user_id', $authUser->id)
			->where('parcel_id', $validated['parcel_id'])
			->first();

		if (!$parcel && preg_match('/^SID-(\d+)$/', $validated['parcel_id'], $matches)) {
			$parcel = ParcelDetail::query()
				->where('user_id', $authUser->id)
				->where('id', (int) $matches[1])
				->first();
		}

		if (!$parcel) {
			return response()->json([
				'status' => false,
				'message' => 'Parcel not found for this user.',
			], 404);
		}

		$parcel->update([
			'status' => $validated['status'],
		]);

		return response()->json([
			'status' => true,
			'message' => 'Parcel status updated successfully.',
			'data' => [
				'id' => $parcel->id,
				'parcel_id' => $parcel->parcel_id ?? sprintf('SID-%07d', $parcel->id),
				'assignment_parcel_id' => $parcel->assignment_parcel_id,
				'user_id' => $parcel->user_id,
				'status' => $parcel->status,
				'updated_at' => optional($parcel->updated_at)->format('Y-m-d H:i:s'),
			],
		]);
	}

	public function todayUserParcels(Request $request)
	{
		$vehicleTypeColumn = $this->getVehicleTypeColumn();
		$vehicleColumns = ['id', 'vehicle_number'];
		if ($vehicleTypeColumn) {
			$vehicleColumns[] = $vehicleTypeColumn;
		}

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
				'message' => 'You can only view your own parcel entries.',
			], 403);
		}

		$today = Carbon::today()->toDateString();

		$parcels = ParcelDetail::with([
			'assignmentParcel:id,vendor_id,vehicle_id,user_id,hub_id,assignment_date,status',
			'assignmentParcel.vendor:id,name',
			'assignmentParcel.vehicle:' . implode(',', $vehicleColumns),
			'assignmentParcel.hub:id,name',
		])
			->where('user_id', $userId)
			->whereDate('created_at', $today)
			->orWhere('status', '=', 'Pending')
			->orderByDesc('id')
			->get();

		$data = $parcels->map(function ($parcel) use ($vehicleTypeColumn) {
			return [
				'id' => $parcel->id,
				'parcel_id' => $parcel->parcel_id ?? sprintf('SID-%07d', $parcel->id),
				'assignment_parcel_id' => $parcel->assignment_parcel_id,
				'status' => $parcel->status,
				'created_at' => optional($parcel->created_at)->format('Y-m-d H:i:s'),
				'assignment_date' => optional($parcel->assignmentParcel?->assignment_date)->format('Y-m-d'),
				'vendor_name' => $parcel->assignmentParcel?->vendor?->name,
				'vehicle_number' => $parcel->assignmentParcel?->vehicle?->vehicle_number,
				'vehicle_type' => $vehicleTypeColumn
					? data_get($parcel, "assignmentParcel.vehicle.{$vehicleTypeColumn}")
					: null,
				'hub_name' => $parcel->assignmentParcel?->hub?->name,
			];
		})->values();

		$statusCounts = [
			'assigned' => $parcels->where('status', 'assigned')->count(),
			'delivered' => $parcels->where('status', 'delivered')->count(),
			'pending' => $parcels->where('status', 'pending')->count(),
		];

		return response()->json([
			'status' => true,
			'date' => $today,
			'user_id' => $userId,
			'total' => $data->count(),
			'status_counts' => $statusCounts,
			'data' => $data,
		]);
	}

	private function getVehicleTypeColumn(): ?string
	{
		foreach (['type', 'vehicle_type', 'model', 'vehicle_model'] as $column) {
			if (Schema::hasColumn('vehicles', $column)) {
				return $column;
			}
		}

		return null;
	}
}


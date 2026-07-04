 <?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PunchController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ParcelController;
use App\Http\Controllers\Api\VehicalController;
use App\Http\Controllers\Api\FaqController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('logout', [AuthController::class, 'logout']);
Route::get('/salary-slip/{employee_id}/{date}', [AttendanceController::class, 'salarySlipView']);
Route::get('/salary-slip-pdf/{employee_id}/{date}', [AttendanceController::class, 'salarySlipPdf']);
Route::middleware('jwt.verify')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
	Route::post('roles', [RoleController::class, 'getRole']);
	Route::get('settings', [SettingController::class, 'index']);
	Route::post('punch-in', [PunchController::class, 'punchIn']);
	Route::post('punch-out', [PunchController::class, 'punchOut']);
	Route::post('attendence', [UserController::class, 'getAttendence']);
	Route::post('attendance-record', [AttendanceController::class, 'attendanceRecord']);
	Route::post('salary-record', [AttendanceController::class, 'salaryRecord']);
	// Route::get('/salary-slip-pdf/{employee_id}/{date}', [AttendanceController::class, 'salarySlipPdf']);
	Route::get('todaysparcel', [ParcelController::class, 'todayUserParcels']);
	Route::post('update_parcel', [ParcelController::class, 'updateParcelStatus']);
	Route::post('vehical_store', [VehicalController::class, 'storeUsage']);
	Route::get('usage_list', [VehicalController::class, 'usageList']);
	Route::get('/static-pages', [SettingController::class, 'getStaticPages']);
	Route::get('faqs', [FaqController::class, 'index']);
});






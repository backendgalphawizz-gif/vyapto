<?php

use App\Http\Controllers\Portal\HomeController;
use App\Http\Controllers\Portal\AttendanceController;
use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\FaqController;
use App\Http\Controllers\Portal\PageController;
use App\Http\Controllers\Portal\ParcelController;
use App\Http\Controllers\Portal\ProfileController;
use App\Http\Controllers\Portal\PunchController;
use App\Http\Controllers\Portal\SalaryController;
use App\Http\Controllers\Portal\VehicleUsageController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/login', [PortalAuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [PortalAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [PortalAuthController::class, 'register'])->name('register.submit');
    Route::post('/login/otp/send', [PortalAuthController::class, 'sendOtp'])->name('login.otp.send');
    Route::post('/login/otp/verify', [PortalAuthController::class, 'verifyOtp'])->name('login.otp.verify');
    Route::post('/login/email', [PortalAuthController::class, 'loginWithEmail'])->name('login.email');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified', 'app.user'])
    ->prefix('portal')
    ->name('portal.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/punch', [PunchController::class, 'index'])->name('punch.index');
        Route::post('/punch-in', [PunchController::class, 'punchIn'])->name('punch.in');
        Route::post('/punch-out', [PunchController::class, 'punchOut'])->name('punch.out');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/parcels', [ParcelController::class, 'index'])->name('parcels.index');
        Route::post('/parcels/status', [ParcelController::class, 'updateStatus'])->name('parcels.update-status');

        Route::get('/vehicle-usage', [VehicleUsageController::class, 'index'])->name('vehicle-usage.index');
        Route::get('/vehicle-usage/create', [VehicleUsageController::class, 'create'])->name('vehicle-usage.create');
        Route::post('/vehicle-usage', [VehicleUsageController::class, 'store'])->name('vehicle-usage.store');

        Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
        Route::get('/salary-slip/{date}', [SalaryController::class, 'show'])->name('salary.show');

        Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');

        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{key}', [PageController::class, 'show'])->name('pages.show');
    });

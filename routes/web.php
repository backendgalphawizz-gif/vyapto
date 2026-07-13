<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\SalarySlipController;
use App\Http\Controllers\Admin\HubController as AdminHubController;
use App\Http\Controllers\Admin\AssignmentParcelController;
use App\Http\Controllers\Admin\VehicleUsageController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\FaqCategoryController as AdminFaqCategoryController;
use App\Http\Controllers\Admin\WebsitePageSectionController;
use App\Http\Controllers\Admin\WebsiteServiceController;
use App\Http\Controllers\Admin\WebsiteProductController;
use App\Http\Controllers\Admin\WebsiteCareerItemController;
use App\Http\Controllers\Admin\WebsiteBlogController;
use App\Http\Controllers\Admin\WebsiteContactMessageController;

// Public website routes are in website.php; portal routes in portal.php

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user && $user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('portal.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // i want prefix company setting and then inside that i want to have permissions, roles, users, vehicles, attendance, announcements
    Route::group(['middleware' => ['permission:manage_settings']], function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'companyInfo'])->name('company-info');
            Route::post('update-info', [SettingsController::class, 'updateInfo'])->name('update-info');
        });
        Route::resource('admin/static-pages', SettingsController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::prefix('admin/website')->name('admin.website.')->group(function () {
            Route::get('page-sections', [WebsitePageSectionController::class, 'index'])->name('page-sections.index');
            Route::get('page-sections/create', [WebsitePageSectionController::class, 'create'])->name('page-sections.create');
            Route::post('page-sections', [WebsitePageSectionController::class, 'store'])->name('page-sections.store');
            Route::get('page-sections/{pageSection}/edit', [WebsitePageSectionController::class, 'edit'])->name('page-sections.edit');
            Route::put('page-sections/{pageSection}', [WebsitePageSectionController::class, 'update'])->name('page-sections.update');
            Route::resource('services', WebsiteServiceController::class)->except(['show']);
            Route::resource('products', WebsiteProductController::class)->except(['show']);
            Route::resource('careers', WebsiteCareerItemController::class)->except(['show'])->parameters(['careers' => 'career']);
            Route::resource('blogs', WebsiteBlogController::class)->except(['show']);
            Route::get('contact-messages', [WebsiteContactMessageController::class, 'index'])->name('contact-messages.index');
            Route::get('contact-messages/{contactMessage}', [WebsiteContactMessageController::class, 'show'])->name('contact-messages.show');
            Route::patch('contact-messages/{contactMessage}/status', [WebsiteContactMessageController::class, 'updateStatus'])->name('contact-messages.update-status');
            Route::delete('contact-messages/{contactMessage}', [WebsiteContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
        });
    });

    Route::group(['middleware' => ['permission:manage_permissions']], function () {
        Route::post('permissions/filter', [PermissionController::class, 'filter'])->name('permissions.filter');
        Route::resource('permissions', PermissionController::class);
    });

    Route::group(['middleware' => ['permission:manage_roles']], function () {
        Route::resource('roles', RoleController::class);
    });

    Route::group(['middleware' => ['permission:manage_vehicles']], function () {
        Route::resource('vehicles', VehicleController::class);
        Route::post('vehicles/update-status', [VehicleController::class, 'updateStatus'])
            ->name('vehicles.updateStatus');
        Route::get('vehicles-export', [VehicleController::class, 'export'])
            ->name('vehicles.export');
    });

    Route::group(['middleware' => ['permission:manage_attendance']], function () {
        Route::post('attendance/filter', [AttendanceController::class, 'filter'])->name('attendance.filter');
        Route::get('attendance-report', [AttendanceController::class, 'report'])->name('attendance.report');
        Route::resource('attendance', AttendanceController::class);
    });

    Route::group(['middleware' => ['permission:manage_vendors']], function () {
        Route::post('/vendors/update-status', [VendorController::class, 'updateStatus'])->name('vendors.updateStatus');
        Route::get('/vendors-export', [VendorController::class, 'export'])->name('vendors.export');
        Route::resource('vendors', VendorController::class);
    });

    Route::group(['middleware' => ['permission:manage_salary_slips']], function () {
        Route::resource('salary-slips', SalarySlipController::class);

        // User Salary CRUD
        Route::get('user-salaries',                          [SalarySlipController::class, 'salaryIndex'])->name('user-salaries.index');
        Route::post('user-salaries',                         [SalarySlipController::class, 'salaryStore'])->name('user-salaries.store');
        Route::put('user-salaries/{id}',                     [SalarySlipController::class, 'salaryUpdate'])->name('user-salaries.update');
        Route::delete('user-salaries/{id}',                  [SalarySlipController::class, 'salaryDestroy'])->name('user-salaries.destroy');
        Route::post('user-salaries/{id}/generate-slip',      [SalarySlipController::class, 'generateSlip'])->name('user-salaries.generate-slip');
    });

    Route::group(['middleware' => ['permission:manage_employees']], function () {
        Route::post('/employees/update-status', [UserController::class, 'updateStatus'])->name('employees.updateStatus');
        Route::get('/employees-report', [UserController::class, 'report'])->name('employees.report');
        Route::resource('employees', UserController::class);
    });
   
    // deprtments
    Route::post('/departments/update-status', [DepartmentController::class, 'updateStatus'])->name('departments.updateStatus');
    Route::get('/departments-export', [DepartmentController::class, 'export'])->name('departments.export');
    Route::resource('departments', DepartmentController::class);



    Route::get('/register-user', [RegisterController::class, 'showAdminRegisterForm'])->name('admin.register.form');

    // Handle registration and OTP
    Route::post('/register-user', [RegisterController::class, 'registerByAdmin'])->name('admin.register.user');
    Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('admin.otp.verify.form');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('admin.otp.verify.submit');

    // Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    // Employee
    Route::get('/employee/dashboard', [EmployeeController::class, 'index'])->name('employee.dashboard');
    // Learner
    Route::get('/learner/dashboard', [LearnerController::class, 'index'])->name('learner.dashboard');


    

    // User Management
    /*Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/sendmail', [UserController::class, 'sendMail'])->name('users.sendmail');
    Route::get('/users/sendmail', fn() => redirect()->route('users.index'));*/







    // Email Logs
    Route::get('/email-logs', [EmailLogController::class, 'index'])->name('email.logs');

    // Custom Email
    Route::get('/custom-email', [UserController::class, 'customEmailForm'])->name('email.custom.form');
    Route::post('/custom-email/send', [UserController::class, 'sendCustomEmail'])->name('email.custom.send');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Edit the User Profile
    Route::get('/admin/profile/edit', [ProfileController::class, 'edit'])
        ->middleware('auth')
        ->name('admin.profile.edit');

    Route::patch('/admin/profile/update', [ProfileController::class, 'update'])
        ->middleware('auth')
        ->name('admin.profile.update');

    // Update password from admin profile page
    Route::put('/admin/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('auth')
        ->name('admin.profile.password');

    // Delete account from admin profile page
    Route::delete('/admin/profile/delete', [ProfileController::class, 'destroy'])
        ->middleware('auth')
        ->name('admin.profile.destroy');
});

Route::middleware(['auth', 'verified'])
    ->prefix('admin/announcements')
    ->name('admin.announcements.')
    ->group(function () {

        // List & create announcements
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');

        // Show send form
        Route::get('/send', [AnnouncementController::class, 'sendForm'])->name('sendForm');

        //  Process sending to selected recipients
        Route::post('/send', [AnnouncementController::class, 'processSend'])->name('processSend');

        // Send a specific announcement by ID (e.g. quick resend)
        Route::get('/{id}/send', [AnnouncementController::class, 'send'])->name('send');

        // View logs
        Route::get('/logs', [AnnouncementController::class, 'logs'])->name('logs');
    });



// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('hubs', AdminHubController::class);
    Route::get('hubs-map', [AdminHubController::class, 'map'])->name('hubs.map');
    Route::get('hubs-export', [AdminHubController::class, 'export'])->name('hubs.export');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Existing routes...
    
    // Assignment Parcel routes
    Route::resource('assignment-parcel', AssignmentParcelController::class);
    Route::post('assignment-parcel/{assignmentParcel}/update-status', [AssignmentParcelController::class, 'updateStatus'])->name('assignment-parcel.update-status');
    Route::get('assignment-parcel-report', [AssignmentParcelController::class, 'report'])->name('assignment-parcel.report');
    Route::get('assignment-parcel-export', [AssignmentParcelController::class, 'export'])->name('assignment-parcel.export');
    Route::get('vehicle-usage-today-km-summary', [VehicleUsageController::class, 'todayKmSummary'])
        ->name('vehicle-usage.today-km-summary');
    Route::resource('vehicle-usage', VehicleUsageController::class);
    Route::get('vehicle-usage-export', [VehicleUsageController::class, 'export'])->name('vehicle-usage.export');
    Route::resource('faq-categories', AdminFaqCategoryController::class);
    Route::resource('faqs', AdminFaqController::class);
});


// Temporarily allow public access for testing purposes~
Route::resource('learners', LearnerController::class)->names('admin.learners');
// Route::resource('employees', EmployeeController::class);
// Route::resource('attendance', AttendanceController::class);
// Route::resource('announcements', AnnouncementController::class);
Route::delete('/learners/{id}', [LearnerController::class, 'destroy'])->name('learners.destroy');






require __DIR__ . '/auth.php';
require __DIR__ . '/website.php';
require __DIR__ . '/portal.php';

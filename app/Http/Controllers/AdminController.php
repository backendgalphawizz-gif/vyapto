<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MailLog;   
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $learnerCount = DB::table('learners')->count();
        // $employeeCount = DB::table('employees')->count();
        $mailLogCount = DB::table('email_logs')->count();
        $announcementCount = DB::table('announcements')->count();
        $attendanceCount = DB::table('learner_attendance')->count();

        return view('admin.dashboard', compact(
            'userCount',
            'learnerCount',
            // 'employeeCount',
            'announcementCount',
            'attendanceCount',
            'mailLogCount'
        ));
    }
}

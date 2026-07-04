<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\EmployeePunchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PunchController extends Controller
{
    public function index(EmployeePunchService $punchService)
    {
        $user = Auth::user();
        $attendance = $punchService->todayAttendance($user);

        return view('portal.punch.index', compact('user', 'attendance'));
    }

    public function punchIn(Request $request, EmployeePunchService $punchService)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string|max:500',
            'exception' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $result = $punchService->punchIn(Auth::user(), $validated, $request->file('image'));

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('portal.dashboard')->with('success', $result['message']);
    }

    public function punchOut(Request $request, EmployeePunchService $punchService)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string|max:500',
            'exception' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $result = $punchService->punchOut(Auth::user(), $validated, $request->file('image'));

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('portal.dashboard')->with('success', $result['message']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\AnnouncementTarget;
use App\Models\AnnouncementLog;
use App\Models\Learner;
use App\Mail\AnnouncementEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sent_by' => 'required|string'
        ]);

        Announcement::create($request->only('title', 'content', 'sent_by'));

        return redirect()->back()->with('success', 'Announcement created successfully!');
    }

    public function sendForm()
    {
        $announcements = Announcement::latest()->get(); // So admin can select one
        $gradeLevels = Learner::select('grade_level')->distinct()->pluck('grade_level');
        $sections = Learner::select('section')->distinct()->pluck('section');

        return view('admin.announcements.send', compact('announcements', 'gradeLevels', 'sections'));
    }

    public function processSend(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'grade_level' => 'nullable|string',
            'section' => 'nullable|string',
        ]);

        $announcement = Announcement::findOrFail($request->announcement_id);

        // Save the target information
        AnnouncementTarget::create([
            'announcement_id' => $announcement->id,
            'grade_level' => $request->grade_level ?? 'All',
            'section' => $request->section ?? 'All',
        ]);

        // Get recipients
        $query = Learner::query();
        if ($request->grade_level) {
            $query->where('grade_level', $request->grade_level);
        }
        if ($request->section) {
            $query->where('section', $request->section);
        }
        $recipients = $query->get();

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new AnnouncementEmail($announcement));

                // Log the successful email sending
                AnnouncementLog::create([
                    'announcement_id' => $announcement->id,
                    'learner_id' => $recipient->id,
                    'is_sent' => true,
                    'sent_at' => Carbon::now(),
                ]);

            } catch (\Exception $e) {
                // Log a failed attempt if needed
                AnnouncementLog::create([
                    'announcement_id' => $announcement->id,
                    'learner_id' => $recipient->id,
                    'is_sent' => false,
                    'sent_at' => null,
                ]);
            }
        }

        return redirect()->route('admin.announcements.sendForm')
                        ->with('success', 'Announcement sent and logged successfully.');
    }

    public function logs()
    {
        $logs = AnnouncementLog::with(['announcement', 'learner'])
            ->orderByDesc('sent_at')
            ->paginate(10);

        return view('admin.announcements.logs', compact('logs'));
    }


    public function send($id)
    {
        $announcement = Announcement::findOrFail($id);

        $targets = AnnouncementTarget::where('announcement_id', $announcement->id)->get();

        $recipients = collect();

        foreach ($targets as $target) {
            $matched = Learner::where('grade_level', $target->grade_level)
                ->where('section', $target->section)
                ->get();

            $recipients = $recipients->merge($matched);
        }

        foreach ($recipients as $learner) {
            // Check if learner has email before sending
            if ($learner->email) {
                Mail::to($learner->email)->send(new AnnouncementEmail($announcement));
            }
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement sent to selected learners.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        //
    }
}

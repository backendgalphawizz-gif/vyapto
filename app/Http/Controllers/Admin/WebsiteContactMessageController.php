<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContactMessage;
use Illuminate\Http\Request;

class WebsiteContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = WebsiteContactMessage::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->paginate(20);

        return view('admin.website.contact-messages.index', compact('messages'));
    }

    public function show(WebsiteContactMessage $contactMessage)
    {
        if ($contactMessage->status === WebsiteContactMessage::STATUS_NEW) {
            $contactMessage->update(['status' => WebsiteContactMessage::STATUS_READ]);
        }

        return view('admin.website.contact-messages.show', ['message' => $contactMessage]);
    }

    public function updateStatus(Request $request, WebsiteContactMessage $contactMessage)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied',
        ]);

        $contactMessage->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function destroy(WebsiteContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.website.contact-messages.index')->with('success', 'Message deleted.');
    }
}

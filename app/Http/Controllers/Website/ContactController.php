<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsiteContactMessage;
use App\Models\WebsitePageSection;
use Illuminate\Http\Request;

class ContactController extends BaseWebsiteController
{
    public function index()
    {
        $sections = WebsitePageSection::sectionsFor('contact');

        return view('website.contact', array_merge($this->sharedData(), compact('sections')));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        WebsiteContactMessage::create([
            ...$validated,
            'status' => WebsiteContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('website.contact')
            ->with('success', 'Thank you! Your message has been sent. We will get back to you soon.');
    }
}

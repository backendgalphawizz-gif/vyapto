<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsiteCareerItem;
use App\Models\WebsiteContactMessage;
use App\Models\WebsitePageSection;
use Illuminate\Http\Request;

class CareerController extends BaseWebsiteController
{
    public function index()
    {
        $sections = WebsitePageSection::sectionsFor('careers');

        $lifeAtVyapto = WebsiteCareerItem::active()
            ->category(WebsiteCareerItem::CATEGORY_LIFE)
            ->ordered()
            ->get();

        $deliveryPartners = WebsiteCareerItem::active()
            ->category(WebsiteCareerItem::CATEGORY_DELIVERY_PARTNER)
            ->ordered()
            ->get();

        $openings = WebsiteCareerItem::active()
            ->category(WebsiteCareerItem::CATEGORY_JOB_OPENING)
            ->ordered()
            ->get();

        $news = WebsiteCareerItem::active()
            ->category(WebsiteCareerItem::CATEGORY_NEWS)
            ->ordered()
            ->get();

        return view('website.careers', array_merge($this->sharedData(), compact(
            'sections',
            'lifeAtVyapto',
            'deliveryPartners',
            'openings',
            'news'
        )));
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
        ]);

        $contact = trim($validated['contact']);
        $isEmail = (bool) filter_var($contact, FILTER_VALIDATE_EMAIL);

        WebsiteContactMessage::create([
            'name' => $validated['name'],
            'email' => $isEmail ? $contact : 'careers@vyapto.com',
            'phone' => $isEmail ? null : $contact,
            'subject' => 'Career Application: '.$validated['category'],
            'message' => "Career application submitted via careers page.\n\n"
                ."Name: {$validated['name']}\n"
                ."Category: {$validated['category']}\n"
                .'Contact: '.$contact,
            'status' => WebsiteContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('website.careers')
            ->with('career_applied', true)
            ->withFragment('apply');
    }
}

<?php

namespace App\Http\Controllers\Website;

use App\Models\Faq;

class FaqController extends BaseWebsiteController
{
    public function index()
    {
        $sections = \App\Models\WebsitePageSection::sectionsFor('faq');

        $faqs = Faq::with('category:id,name')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('website.faq', array_merge($this->sharedData(), compact('sections', 'faqs')));
    }
}

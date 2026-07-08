<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsitePageSection;
use App\Models\WebsiteService;

class HomeController extends BaseWebsiteController
{
    public function index()
    {
        $sections = WebsitePageSection::sectionsFor('home');
        $services = WebsiteService::active()->ordered()->limit(4)->get();
        $faqs = \App\Models\Faq::where('status', 1)->orderBy('sort_order')->limit(5)->get();

        return view('website.home', array_merge($this->sharedData(), compact('sections', 'services', 'faqs')));
    }
}

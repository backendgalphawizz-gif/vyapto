<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsitePageSection;
use App\Models\WebsiteService;

class HomeController extends BaseWebsiteController
{
    public function index()
    {
        $sections = WebsitePageSection::sectionsFor('home');
        $services = WebsiteService::active()->ordered()->limit(5)->get();

        return view('website.home', array_merge($this->sharedData(), compact('sections', 'services')));
    }
}

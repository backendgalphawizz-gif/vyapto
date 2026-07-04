<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsitePageSection;
use App\Models\WebsiteService;

class ServiceController extends BaseWebsiteController
{
    public function index()
    {
        $sections = WebsitePageSection::sectionsFor('services');
        $services = WebsiteService::active()->ordered()->get();

        return view('website.services', array_merge($this->sharedData(), compact('sections', 'services')));
    }

    public function show(string $slug)
    {
        $service = WebsiteService::active()->where('slug', $slug)->firstOrFail();

        return view('website.service-show', array_merge($this->sharedData(), compact('service')));
    }
}

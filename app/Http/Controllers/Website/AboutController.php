<?php

namespace App\Http\Controllers\Website;

class AboutController extends BaseWebsiteController
{
    public function index()
    {
        $sections = \App\Models\WebsitePageSection::sectionsFor('about');

        return view('website.about', array_merge($this->sharedData(), compact('sections')));
    }
}

<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsiteCareerItem;
use App\Models\WebsitePageSection;

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
}

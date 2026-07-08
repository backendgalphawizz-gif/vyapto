<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WebsitePageSection;
use App\Models\WebsiteService;
use App\Support\BrandAssets;

abstract class BaseWebsiteController extends Controller
{
    protected function sharedData(): array
    {
        $globalSections = WebsitePageSection::sectionsFor('global');

        return [
            'companyName' => BrandAssets::companyName(),
            'companyEmail' => Setting::where('type', 'company_email')->value('value') ?? 'support@vyapto.com',
            'companyPhone' => Setting::where('type', 'company_phone')->value('value') ?? '',
            'companyAddress' => Setting::where('type', 'company_address')->value('value') ?? '',
            'globalSections' => $globalSections,
            'siteLogoDesktop' => BrandAssets::siteLogoDesktop(),
            'siteLogoMobile' => BrandAssets::siteLogoMobile(),
            'siteLogoFooter' => BrandAssets::siteLogoFooter(),
            'navServices' => WebsiteService::active()->ordered()->get(),
            'footerTagline' => $globalSections->get('footer_tagline')?->content
                ?? 'Professional logistics and workforce solutions for businesses across the globe.',
            'socialInstagram' => $globalSections->get('social_instagram')?->link,
            'socialLinkedin' => $globalSections->get('social_linkedin')?->link,
            'socialFacebook' => $globalSections->get('social_facebook')?->link,
        ];
    }
}

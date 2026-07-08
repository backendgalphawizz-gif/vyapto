<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WebsitePageSection;
use App\Models\WebsiteService;

abstract class BaseWebsiteController extends Controller
{
    protected function sharedData(): array
    {
        $globalSections = WebsitePageSection::sectionsFor('global');
        $companyLogo = Setting::where('type', 'company_web_logo')->value('value');

        return [
            'companyName' => Setting::where('type', 'company_name')->value('value') ?? 'Vyapto',
            'companyEmail' => Setting::where('type', 'company_email')->value('value') ?? 'support@vyapto.com',
            'companyPhone' => Setting::where('type', 'company_phone')->value('value') ?? '',
            'companyAddress' => Setting::where('type', 'company_address')->value('value') ?? '',
            'globalSections' => $globalSections,
            'siteLogoDesktop' => $globalSections->get('site_logo_desktop')?->imageUrl()
                ?? ($companyLogo ? asset('storage/company/' . $companyLogo) : asset('images/nav-logo.png')),
            'siteLogoMobile' => $globalSections->get('site_logo_mobile')?->imageUrl()
                ?? ($companyLogo ? asset('storage/company/' . $companyLogo) : asset('images/nav-logo-mobile.png')),
            'siteLogoFooter' => $globalSections->get('site_logo_footer')?->imageUrl()
                ?? ($companyLogo ? asset('storage/company/' . $companyLogo) : asset('images/nav-logo.png')),
            'navServices' => WebsiteService::active()->ordered()->get(),
            'footerTagline' => $globalSections->get('footer_tagline')?->content
                ?? 'Professional logistics and workforce solutions for businesses across the globe.',
            'socialInstagram' => $globalSections->get('social_instagram')?->link,
            'socialLinkedin' => $globalSections->get('social_linkedin')?->link,
            'socialFacebook' => $globalSections->get('social_facebook')?->link,
        ];
    }
}

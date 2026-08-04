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
        $t = fn (string $key, string $fallback = '') => trim((string) ($globalSections->get($key)?->title ?? '')) ?: $fallback;

        return [
            'companyName' => BrandAssets::companyName(),
            'companyEmail' => Setting::where('type', 'company_email')->value('value') ?? '',
            'companyPhone' => Setting::where('type', 'company_phone')->value('value') ?? '',
            'companyAddress' => Setting::where('type', 'company_address')->value('value') ?? '',
            'globalSections' => $globalSections,
            'siteLogoDesktop' => BrandAssets::siteLogoDesktop(),
            'siteLogoMobile' => BrandAssets::siteLogoMobile(),
            'siteLogoFooter' => BrandAssets::siteLogoFooter(),
            'navServices' => WebsiteService::active()->ordered()->get(),
            'footerTagline' => $globalSections->get('footer_tagline')?->content
                ?: 'Professional logistics and workforce solutions for businesses across the globe.',
            'socialInstagram' => $globalSections->get('social_instagram')?->link,
            'socialLinkedin' => $globalSections->get('social_linkedin')?->link,
            'socialFacebook' => $globalSections->get('social_facebook')?->link,
            'siteLabels' => [
                'nav_home' => $t('nav_home', 'Home'),
                'nav_about' => $t('nav_about', 'About'),
                'nav_services' => $t('nav_services', 'Services'),
                'nav_services_all' => $t('nav_services_all', 'All Services'),
                'nav_products' => $t('nav_products', 'Products'),
                'nav_blogs' => $t('nav_blogs', 'Blog'),
                'nav_careers' => $t('nav_careers', 'Careers'),
                'nav_faq' => $t('nav_faq', 'FAQ'),
                'nav_contact' => $t('nav_contact', 'Contact'),
                'nav_login' => $t('nav_login', 'Employee Login'),
                'nav_cta' => $t('nav_cta', 'Get in Touch'),
                'footer_col_services' => $t('footer_col_services', 'Services'),
                'footer_col_company' => $t('footer_col_company', 'Company'),
                'footer_col_support' => $t('footer_col_support', 'Support'),
                'footer_link_about' => $t('footer_link_about', 'About Us'),
                'footer_link_contact' => $t('footer_link_contact', 'Contact Us'),
                'footer_copyright' => $t('footer_copyright', 'All rights reserved.'),
                'footer_privacy' => $t('footer_privacy', 'Privacy Policy'),
                'footer_terms' => $t('footer_terms', 'Terms of Service'),
            ],
        ];
    }
}

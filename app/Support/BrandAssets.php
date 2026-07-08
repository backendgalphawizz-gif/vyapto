<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\WebsitePageSection;

class BrandAssets
{
    public static function companyName(): string
    {
        return Setting::where('type', 'company_name')->value('value') ?? 'Vyapto';
    }

    public static function companyWebLogoPath(): ?string
    {
        return Setting::where('type', 'company_web_logo')->value('value');
    }

    public static function companyWebLogoUrl(): ?string
    {
        $logo = self::companyWebLogoPath();

        return $logo ? asset('storage/company/' . $logo) : null;
    }

    public static function siteLogoUrl(
        string $sectionKey = 'site_logo_desktop',
        ?string $fallback = 'images/nav-logo.png'
    ): string {
        $sectionUrl = WebsitePageSection::sectionsFor('global')->get($sectionKey)?->imageUrl();

        if ($sectionUrl) {
            return $sectionUrl;
        }

        return self::companyWebLogoUrl() ?? asset($fallback);
    }

    public static function siteLogoDesktop(): string
    {
        return self::siteLogoUrl('site_logo_desktop', 'images/nav-logo.png');
    }

    public static function siteLogoMobile(): string
    {
        return self::siteLogoUrl('site_logo_mobile', 'images/nav-logo-mobile.png');
    }

    public static function siteLogoFooter(): string
    {
        return self::siteLogoUrl('site_logo_footer', 'images/nav-logo.png');
    }
}

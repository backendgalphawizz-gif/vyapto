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

    /**
     * Absolute path to company logo on disk (for PDF / payslip embedding).
     */
    public static function companyWebLogoAbsolutePath(): ?string
    {
        $logo = self::companyWebLogoPath();
        if (! $logo) {
            return null;
        }

        $logo = ltrim(str_replace('\\', '/', $logo), '/');

        foreach ([
            storage_path('app/public/company/' . $logo),
            public_path('storage/company/' . $logo),
        ] as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Logo as data URI — works in DomPDF and inline PDF viewers without /storage/ HTTP access.
     */
    public static function companyWebLogoEmbedUrl(): ?string
    {
        $path = self::companyWebLogoAbsolutePath();
        if (! $path) {
            return self::companyWebLogoUrl();
        }

        $mime = @mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
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

    /**
     * Logo optimized for dark admin sidebar (white wordmark + truck).
     */
    public static function siteLogoAdmin(): string
    {
        $sectionUrl = WebsitePageSection::sectionsFor('global')->get('site_logo_admin')?->imageUrl();

        if ($sectionUrl) {
            return $sectionUrl;
        }

        $dark = public_path('images/nav-logo-dark.png');
        if (is_file($dark)) {
            return asset('images/nav-logo-dark.png');
        }

        return self::siteLogoDesktop();
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

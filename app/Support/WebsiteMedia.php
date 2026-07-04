<?php

namespace App\Support;

class WebsiteMedia
{
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            return $fallback ? asset($fallback) : null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, '/images/')) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}

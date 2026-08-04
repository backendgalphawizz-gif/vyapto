<?php

namespace App\Support;

class WebsiteMedia
{
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if (empty($path)) {
            return $fallback ? self::url($fallback) : null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Normalize accidental relative paths like ../../../images/foo.avif
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#^(\.\./)+#', '', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}

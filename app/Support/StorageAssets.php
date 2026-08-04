<?php

namespace App\Support;

class StorageAssets
{
    public static function url(?string $path, ?string $fallback = null): ?string
    {
        if ($path === null || trim($path) === '') {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#^(\.\./)+#', '', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'images/') || str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'app/public/')) {
            $path = substr($path, strlen('app/public/'));
        }

        if ($path === '') {
            return $fallback;
        }

        if (self::absolutePath($path)) {
            return route('admin.media.show', ['path' => $path]);
        }

        return $fallback;
    }

    public static function absolutePath(string $path): ?string
    {
        $path = self::normalizeRelativePath($path);
        if ($path === '') {
            return null;
        }

        foreach ([
            storage_path('app/public/'.$path),
            public_path('storage/'.$path),
        ] as $full) {
            if (is_file($full)) {
                return $full;
            }
        }

        return null;
    }

    public static function normalizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#^(\.\./)+#', '', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (str_starts_with($path, 'app/public/')) {
            $path = substr($path, strlen('app/public/'));
        }

        return str_replace(['..', '\\'], '', $path);
    }
}

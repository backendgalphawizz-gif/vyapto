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

        $relative = self::normalizeRelativePath($path);
        if ($relative === '') {
            return $fallback;
        }

        if (auth()->check()) {
            return self::adminMediaUrl($relative);
        }

        return self::publicUrl($relative, $fallback);
    }

    public static function publicUrl(?string $path, ?string $fallback = null): ?string
    {
        if ($path === null || trim($path) === '') {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $relative = self::normalizeRelativePath($path);
        if ($relative === '') {
            return $fallback;
        }

        $symlinkFile = public_path('storage/'.$relative);
        if (is_file($symlinkFile)) {
            return asset('storage/'.$relative);
        }

        if (self::absolutePath($relative)) {
            return url('/media?'.http_build_query(['path' => $relative]));
        }

        return $fallback;
    }

    public static function adminMediaUrl(string $relativePath): string
    {
        $relativePath = self::normalizeRelativePath($relativePath);

        return '/admin/media?'.http_build_query(['path' => $relativePath]);
    }

    public static function absolutePath(string $path): ?string
    {
        $path = self::normalizeRelativePath($path);
        if ($path === '') {
            return null;
        }

        foreach (self::pathCandidates($path) as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public static function pathCandidates(string $path): array
    {
        $path = self::normalizeRelativePath($path);
        if ($path === '') {
            return [];
        }

        $candidates = [
            storage_path('app/public/'.$path),
            public_path('storage/'.$path),
        ];

        if (! str_starts_with($path, 'storage/')) {
            $candidates[] = storage_path('app/public/storage/'.$path);
            $candidates[] = public_path($path);
        }

        return array_values(array_unique($candidates));
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

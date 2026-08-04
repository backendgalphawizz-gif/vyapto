<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\StorageAssets;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function show(Request $request): BinaryFileResponse
    {
        $path = (string) $request->query('path', '');
        if ($path === '') {
            abort(404);
        }

        $absolute = StorageAssets::absolutePath($path);
        if (! $absolute) {
            abort(404);
        }

        return response()->file($absolute, [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}

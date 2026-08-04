<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\StorageAssets;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function show(string $path): BinaryFileResponse
    {
        $absolute = StorageAssets::absolutePath($path);

        if (! $absolute) {
            abort(404);
        }

        return response()->file($absolute);
    }
}

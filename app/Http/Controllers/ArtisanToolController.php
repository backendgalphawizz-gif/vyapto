<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ArtisanToolController extends Controller
{
    /**
     * Run optimize:clear + storage:link via URL.
     *
     * Example:
     * /run-artisan?key=YOUR_SECRET_KEY
     * /run-artisan?key=YOUR_SECRET_KEY&action=clear
     * /run-artisan?key=YOUR_SECRET_KEY&action=link
     * /run-artisan?key=YOUR_SECRET_KEY&action=all
     */
    public function run(Request $request)
    {
        $configuredKey = (string) config('app.artisan_tool_key');

        if ($configuredKey === '' || ! hash_equals($configuredKey, (string) $request->query('key', ''))) {
            abort(403, 'Invalid or missing key.');
        }

        $action = strtolower((string) $request->query('action', 'all'));
        if (! in_array($action, ['all', 'clear', 'link'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid action. Use action=all|clear|link',
            ], 422);
        }

        $results = [];

        try {
            if (in_array($action, ['all', 'clear'], true)) {
                Artisan::call('optimize:clear');
                $results['optimize:clear'] = trim(Artisan::output()) ?: 'Caches cleared.';
            }

            if (in_array($action, ['all', 'link'], true)) {
                $link = public_path('storage');
                $target = storage_path('app/public');

                if (! File::exists($target)) {
                    File::makeDirectory($target, 0755, true);
                }

                // Remove broken/old public/storage so storage:link can recreate it
                if (is_link($link) || File::exists($link)) {
                    if (is_link($link)) {
                        File::delete($link);
                    } elseif (PHP_OS_FAMILY !== 'Windows' && is_dir($link) && ! File::allFiles($link)) {
                        File::deleteDirectory($link);
                    }
                }

                Artisan::call('storage:link');
                $results['storage:link'] = trim(Artisan::output()) ?: 'Storage link created.';

                if (! is_link($link) && ! File::exists($link)) {
                    @symlink($target, $link);
                    $results['storage:link_fallback'] = (is_link($link) || File::exists($link))
                        ? 'Symlink created via fallback.'
                        : 'Could not create storage link automatically.';
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Artisan commands completed.',
                'action' => $action,
                'results' => $results,
                'time' => now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to run artisan commands.',
                'error' => $e->getMessage(),
                'results' => $results,
            ], 500);
        }
    }
}

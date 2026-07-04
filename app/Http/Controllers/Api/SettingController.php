<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Setting;

class SettingController extends Controller
{
    // GET ALL SETTINGS
    public function index()
    {
        $settings = Setting::all();

        return response()->json([
            'status' => true,
            'data' => $settings
        ]);
    }
    public function getStaticPages()
    {
        $pages = \DB::table('static_pages')
            ->where('status', 1)
            ->get();

        $formatted = $pages->map(function ($page) {
            return [
                'id' => $page->id,
                'name' => $page->title, 
                'slug' => $page->key,   
                'description' => strip_tags($page->content),

                'video_link' => null,
                'video_embed' => null,
                'file_name' => null,
                'hash_name' => null,
                'external_link' => null,

                'type' => 'page', 
                'status' => $page->status == 1 ? 'active' : 'inactive',
                'language_setting_id' => 1,
                'private' => 0,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Static pages fetched successfully',
            'data' => $formatted
        ]);
    }

}
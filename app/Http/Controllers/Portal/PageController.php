<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = StaticPage::where('status', 1)->orderBy('title')->get();

        return view('portal.pages.index', compact('pages'));
    }

    public function show(string $key)
    {
        $page = StaticPage::where('key', $key)->where('status', 1)->firstOrFail();

        return view('portal.pages.show', compact('page'));
    }
}

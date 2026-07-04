<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsiteBlog;
use App\Models\WebsitePageSection;

class BlogController extends BaseWebsiteController
{
    public function index()
    {
        $sections = WebsitePageSection::sectionsFor('blogs');
        $blogs = WebsiteBlog::active()->published()->ordered()->paginate(9);

        return view('website.blogs.index', array_merge($this->sharedData(), compact('sections', 'blogs')));
    }

    public function show(string $slug)
    {
        $blog = WebsiteBlog::active()->published()->where('slug', $slug)->firstOrFail();
        $recent = WebsiteBlog::active()->published()
            ->where('id', '!=', $blog->id)
            ->ordered()
            ->limit(3)
            ->get();

        return view('website.blogs.show', array_merge($this->sharedData(), compact('blog', 'recent')));
    }
}

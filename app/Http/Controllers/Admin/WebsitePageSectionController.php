<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsitePageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WebsitePageSectionController extends Controller
{
    public function index(Request $request)
    {
        $query = WebsitePageSection::query()->orderBy('page')->orderBy('sort_order');

        if ($request->filled('page')) {
            $query->where('page', $request->page);
        }

        $sections = $query->paginate(50)->withQueryString();
        $pages = config('website_sections.pages', ['global', 'home', 'about', 'services', 'products', 'careers', 'blogs', 'contact', 'faq']);

        return view('admin.website.page-sections.index', compact('sections', 'pages'));
    }

    public function create()
    {
        $pages = config('website_sections.pages', []);

        return view('admin.website.page-sections.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $pages = config('website_sections.pages', []);

        $validated = $request->validate([
            'page' => ['required', 'string', Rule::in($pages)],
            'section_key' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('website_page_sections')->where(fn ($q) => $q->where('page', $request->page)),
            ],
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/sections', 'public');
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        WebsitePageSection::create($validated);

        return redirect()->route('admin.website.page-sections.index', ['page' => $validated['page']])
            ->with('success', 'Section created successfully.');
    }

    public function edit(WebsitePageSection $pageSection)
    {
        return view('admin.website.page-sections.edit', ['section' => $pageSection]);
    }

    public function update(Request $request, WebsitePageSection $pageSection)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_image') && $pageSection->image) {
            Storage::disk('public')->delete($pageSection->image);
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($pageSection->image) {
                Storage::disk('public')->delete($pageSection->image);
            }
            $validated['image'] = $request->file('image')->store('website/sections', 'public');
        }

        unset($validated['remove_image']);
        $validated['status'] = $request->boolean('status');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $pageSection->update($validated);

        return redirect()->route('admin.website.page-sections.index', ['page' => $pageSection->page])
            ->with('success', 'Section updated successfully.');
    }
}

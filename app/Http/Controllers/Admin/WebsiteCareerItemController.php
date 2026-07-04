<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteCareerItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteCareerItemController extends Controller
{
    public function index(Request $request)
    {
        $query = WebsiteCareerItem::ordered();

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        $items = $query->paginate(15);
        $categories = WebsiteCareerItem::CATEGORIES;

        return view('admin.website.careers.index', compact('items', 'categories'));
    }

    public function create()
    {
        return view('admin.website.careers.create', ['categories' => WebsiteCareerItem::CATEGORIES]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateItem($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/careers', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        WebsiteCareerItem::create($validated);

        return redirect()->route('admin.website.careers.index')->with('success', 'Career item created.');
    }

    public function edit(WebsiteCareerItem $career)
    {
        return view('admin.website.careers.edit', [
            'item' => $career,
            'categories' => WebsiteCareerItem::CATEGORIES,
        ]);
    }

    public function update(Request $request, WebsiteCareerItem $career)
    {
        $validated = $this->validateItem($request);

        if ($request->boolean('remove_image') && $career->image) {
            Storage::disk('public')->delete($career->image);
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($career->image) {
                Storage::disk('public')->delete($career->image);
            }
            $validated['image'] = $request->file('image')->store('website/careers', 'public');
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $career->update($validated);

        return redirect()->route('admin.website.careers.index')->with('success', 'Career item updated.');
    }

    public function destroy(WebsiteCareerItem $career)
    {
        if ($career->image) {
            Storage::disk('public')->delete($career->image);
        }
        $career->delete();

        return redirect()->route('admin.website.careers.index')->with('success', 'Career item deleted.');
    }

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(WebsiteCareerItem::CATEGORIES)),
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}

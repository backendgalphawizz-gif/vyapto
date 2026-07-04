<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteBlogController extends Controller
{
    public function index()
    {
        $blogs = WebsiteBlog::ordered()->paginate(15);
        return view('admin.website.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.website.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateBlog($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/blogs', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        WebsiteBlog::create($validated);

        return redirect()->route('admin.website.blogs.index')->with('success', 'Blog post created.');
    }

    public function edit(WebsiteBlog $blog)
    {
        return view('admin.website.blogs.edit', compact('blog'));
    }

    public function update(Request $request, WebsiteBlog $blog)
    {
        $validated = $this->validateBlog($request);

        if ($request->boolean('remove_image') && $blog->image) {
            Storage::disk('public')->delete($blog->image);
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $validated['image'] = $request->file('image')->store('website/blogs', 'public');
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $blog->update($validated);

        return redirect()->route('admin.website.blogs.index')->with('success', 'Blog post updated.');
    }

    public function destroy(WebsiteBlog $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return redirect()->route('admin.website.blogs.index')->with('success', 'Blog post deleted.');
    }

    private function validateBlog(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}

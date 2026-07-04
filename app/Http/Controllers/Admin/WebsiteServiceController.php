<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteServiceController extends Controller
{
    public function index()
    {
        $services = WebsiteService::ordered()->paginate(15);
        return view('admin.website.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.website.services.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateService($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/services', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        WebsiteService::create($validated);

        return redirect()->route('admin.website.services.index')->with('success', 'Service created.');
    }

    public function edit(WebsiteService $service)
    {
        return view('admin.website.services.edit', compact('service'));
    }

    public function update(Request $request, WebsiteService $service)
    {
        $validated = $this->validateService($request);

        if ($request->boolean('remove_image') && $service->image) {
            Storage::disk('public')->delete($service->image);
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $validated['image'] = $request->file('image')->store('website/services', 'public');
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $service->update($validated);

        return redirect()->route('admin.website.services.index')->with('success', 'Service updated.');
    }

    public function destroy(WebsiteService $service)
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();

        return redirect()->route('admin.website.services.index')->with('success', 'Service deleted.');
    }

    private function validateService(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}

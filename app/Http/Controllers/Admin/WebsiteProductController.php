<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WebsiteProductController extends Controller
{
    public function index()
    {
        $products = WebsiteProduct::ordered()->paginate(15);
        return view('admin.website.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.website.products.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/products', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        WebsiteProduct::create($validated);

        return redirect()->route('admin.website.products.index')->with('success', 'Product created.');
    }

    public function edit(WebsiteProduct $product)
    {
        return view('admin.website.products.edit', compact('product'));
    }

    public function update(Request $request, WebsiteProduct $product)
    {
        $validated = $this->validateProduct($request);

        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('website/products', 'public');
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $product->update($validated);

        return redirect()->route('admin.website.products.index')->with('success', 'Product updated.');
    }

    public function destroy(WebsiteProduct $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.website.products.index')->with('success', 'Product deleted.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'link' => 'nullable|url|max:500',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}

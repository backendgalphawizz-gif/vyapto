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
        [$validated, $chips] = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website/products', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = $request->boolean('status', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['extra'] = array_filter(['chips' => $chips]);

        WebsiteProduct::create($validated);

        return redirect()->route('admin.website.products.index')->with('success', 'Product created.');
    }

    public function edit(WebsiteProduct $product)
    {
        return view('admin.website.products.edit', compact('product'));
    }

    public function update(Request $request, WebsiteProduct $product)
    {
        [$validated, $chips] = $this->validateProduct($request);

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

        $extra = $product->extra ?? [];
        $extra['chips'] = $chips;
        $validated['extra'] = $extra;

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:120',
            'subtitle' => 'nullable|string|max:180',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'features' => 'nullable|string',
            'chips' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'link' => 'nullable|url|max:500',
            'image' => 'nullable|image|max:5120',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (array_key_exists('features', $validated)) {
            $lines = preg_split('/\r\n|\r|\n/', (string) $validated['features']) ?: [];
            $validated['features'] = array_values(array_filter(array_map('trim', $lines)));
        }

        $chips = [];
        if (array_key_exists('chips', $validated)) {
            $chipLines = preg_split('/\r\n|\r|\n/', (string) ($validated['chips'] ?? '')) ?: [];
            $chips = array_values(array_filter(array_map('trim', $chipLines)));
            unset($validated['chips']);
        }

        return [$validated, $chips];
    }
}

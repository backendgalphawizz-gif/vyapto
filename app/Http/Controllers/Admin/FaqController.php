<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
	public function index()
	{
		$faqs = Faq::query()
			->with('category:id,name')
			->orderBy('sort_order')
			->orderBy('id')
			->paginate(15);

		return view('admin.faqs.index', compact('faqs'));
	}

	public function create()
	{
		$categories = FaqCategory::query()->orderBy('name')->get(['id', 'name']);
		return view('admin.faqs.create', compact('categories'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'faq_category_id' => 'required|exists:faq_categories,id',
			'status' => 'nullable|in:0,1',
			// 'sort_order' => 'nullable|integer|min:0',
			'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
			'image_url' => 'nullable|url|max:1000',
		]);

		if ($request->hasFile('image')) {
			$validated['image'] = $request->file('image')->store('faq-images', 'public');
		}

		$validated['status'] = (int) ($validated['status'] ?? 1);
		$validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

		Faq::create($validated);

		return redirect()->route('admin.faqs.index')
			->with('success', 'FAQ created successfully.');
	}

	public function show(Faq $faq)
	{
		$faq->load('category:id,name');
		return view('admin.faqs.show', compact('faq'));
	}

	public function edit(Faq $faq)
	{
		$categories = FaqCategory::query()->orderBy('name')->get(['id', 'name']);
		return view('admin.faqs.edit', compact('faq', 'categories'));
	}

	public function update(Request $request, Faq $faq)
	{
		$validated = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'faq_category_id' => 'required|exists:faq_categories,id',
			'status' => 'nullable|in:0,1',
			// 'sort_order' => 'nullable|integer|min:0',
			'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
			'image_url' => 'nullable|url|max:1000',
		]);

		if ($request->hasFile('image')) {
			if (!empty($faq->image) && Storage::disk('public')->exists($faq->image)) {
				Storage::disk('public')->delete($faq->image);
			}

			$validated['image'] = $request->file('image')->store('faq-images', 'public');
		}

		$validated['status'] = (int) ($validated['status'] ?? $faq->status ?? 1);
		// $validated['sort_order'] = (int) ($validated['sort_order'] ?? $faq->sort_order ?? 0);

		$faq->update($validated);

		return redirect()->route('admin.faqs.index')
			->with('success', 'FAQ updated successfully.');
	}

	public function destroy(Faq $faq)
	{
		if (!empty($faq->image) && Storage::disk('public')->exists($faq->image)) {
			Storage::disk('public')->delete($faq->image);
		}

		$faq->delete();

		return redirect()->route('admin.faqs.index')
			->with('success', 'FAQ deleted successfully.');
	}
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
	public function index()
	{
		$categories = FaqCategory::query()
			->withCount('faqs')
			->orderBy('name')
			->paginate(15);

		return view('admin.faq-categories.index', compact('categories'));
	}

	public function create()
	{
		return view('admin.faq-categories.create');
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:faq_categories,name',
		]);

		try {
			$faqCategory = new FaqCategory();
			$faqCategory->name = $validated['name'];
			$faqCategory->save();
		} catch (\Throwable $e) {
			return back()->withInput()->with('error', 'Failed to create FAQ category. ' . $e->getMessage());
		}

		return redirect()->route('admin.faq-categories.index')
			->with('success', 'FAQ category created successfully.');
	}

	public function show(FaqCategory $faqCategory)
	{
		$faqCategory->load(['faqs' => function ($query) {
			$query->orderBy('sort_order')->orderBy('id');
		}]);

		return view('admin.faq-categories.show', compact('faqCategory'));
	}

	public function edit(FaqCategory $faqCategory)
	{
		return view('admin.faq-categories.edit', compact('faqCategory'));
	}

	public function update(Request $request, FaqCategory $faqCategory)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:255|unique:faq_categories,name,' . $faqCategory->id,
		]);

		try {
			$faqCategory->name = $validated['name'];
			$faqCategory->save();
		} catch (\Throwable $e) {
			return back()->withInput()->with('error', 'Failed to update FAQ category. ' . $e->getMessage());
		}

		return redirect()->route('admin.faq-categories.index')
			->with('success', 'FAQ category updated successfully.');
	}

	public function destroy(FaqCategory $faqCategory)
	{
		if ($faqCategory->faqs()->exists()) {
			return back()->with('error', 'Cannot delete this category because FAQs exist under it.');
		}

		$faqCategory->delete();

		return redirect()->route('admin.faq-categories.index')
			->with('success', 'FAQ category deleted successfully.');
	}
}

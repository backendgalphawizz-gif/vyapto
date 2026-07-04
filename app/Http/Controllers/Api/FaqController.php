<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
	public function index(): JsonResponse
	{
		$faqs = Faq::query()
			->select([
				'id',
				'title',
				'description',
				'image',
				'faq_category_id',
				'image_url',
			])
			->where('status', 1)
			->orderBy('sort_order')
			->orderBy('id')
			->get();

		return response()->json([
			'status' => true,
			'message' => 'FAQ data fetched successfully',
			'data' => [
				'faqs' => $faqs,
			],
		], 200);
	}
}

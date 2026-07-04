<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::with('category:id,name')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($faq) => $faq->category->name ?? 'General')
            ->sortKeys();

        return view('portal.faqs.index', compact('faqs'));
    }
}

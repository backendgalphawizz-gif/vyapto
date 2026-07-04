<?php

namespace App\Http\Controllers\Website;

use App\Models\WebsitePageSection;
use App\Models\WebsiteProduct;

class ProductController extends BaseWebsiteController
{
    public function index()
    {
        $sections = WebsitePageSection::sectionsFor('products');
        $products = WebsiteProduct::active()->ordered()->get();

        return view('website.products', array_merge($this->sharedData(), compact('sections', 'products')));
    }

    public function show(string $slug)
    {
        $product = WebsiteProduct::active()->where('slug', $slug)->firstOrFail();

        return view('website.product-show', array_merge($this->sharedData(), compact('product')));
    }
}

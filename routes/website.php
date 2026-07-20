<?php

use App\Http\Controllers\Website\AboutController;
use App\Http\Controllers\Website\BlogController;
use App\Http\Controllers\Website\CareerController;
use App\Http\Controllers\Website\ContactController;
use App\Http\Controllers\Website\FaqController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\ServiceController;
use Illuminate\Support\Facades\Route;

Route::name('website.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/our-services', [ServiceController::class, 'index'])->name('services');
    Route::get('/our-services/{slug}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/our-products', [ProductController::class, 'index'])->name('products');
    Route::get('/our-products/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/careers', [CareerController::class, 'index'])->name('careers');
    Route::post('/careers/apply', [CareerController::class, 'apply'])->name('careers.apply');
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
    Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
});

Route::redirect('/website', '/');
Route::get('/website/{path}', function (string $path) {
    return redirect('/' . $path, 301);
})->where('path', '.*');

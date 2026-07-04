<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteProduct extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'link',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (WebsiteProduct $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        return \App\Support\WebsiteMedia::url($this->image);
    }
}

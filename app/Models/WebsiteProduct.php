<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteProduct extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'subtitle',
        'description',
        'content',
        'features',
        'icon',
        'image',
        'link',
        'extra',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'features' => 'array',
        'extra' => 'array',
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

    public function featureList(): array
    {
        $features = $this->features;

        if (is_string($features)) {
            $features = preg_split('/\r\n|\r|\n/', $features) ?: [];
        }

        if (! is_array($features)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($item) => trim((string) $item),
            $features
        )));
    }

    public function extraValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->extra ?? [], $key, $default);
    }

    public function chipList(): array
    {
        $chips = $this->extraValue('chips', []);

        if (is_string($chips)) {
            $chips = preg_split('/\r\n|\r|\n/', $chips) ?: [];
        }

        if (! is_array($chips)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($item) => trim((string) $item),
            $chips
        )));
    }
}

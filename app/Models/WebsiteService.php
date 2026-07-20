<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteService extends Model
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
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'features' => 'array',
    ];

    /**
     * Normalize features from JSON array or newline / comma-separated text.
     */
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

    protected static function booted(): void
    {
        static::creating(function (WebsiteService $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
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

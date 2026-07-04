<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsitePageSection extends Model
{
    protected $fillable = [
        'page',
        'section_key',
        'title',
        'subtitle',
        'content',
        'icon',
        'image',
        'link',
        'extra',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'extra' => 'array',
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeForPage($query, string $page)
    {
        return $query->where('page', $page);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        return \App\Support\WebsiteMedia::url($this->image);
    }

    public static function sectionsFor(string $page): \Illuminate\Support\Collection
    {
        return static::query()
            ->forPage($page)
            ->active()
            ->ordered()
            ->get()
            ->keyBy('section_key');
    }
}

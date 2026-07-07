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
        if ($this->image) {
            return \App\Support\WebsiteMedia::url($this->image);
        }

        $default = $this->extra['default_image'] ?? null;

        return \App\Support\WebsiteMedia::url($default);
    }

    public function label(): string
    {
        return config("website_sections.labels.{$this->page}.{$this->section_key}")
            ?? ucwords(str_replace('_', ' ', $this->section_key));
    }

    public function hint(): ?string
    {
        return config("website_sections.hints.{$this->section_key}");
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

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

    /**
     * Filter by website page slug (home, about, …).
     * Named forWebsitePage — NOT forPage — because Laravel paginate() calls forPage() for LIMIT/OFFSET.
     */
    public function scopeForWebsitePage($query, string $page)
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

    /**
     * Admin form fields actually used on the public website for this section.
     *
     * @return list<string>
     */
    public function editableFields(): array
    {
        $exact = config("website_sections.fields.{$this->page}.{$this->section_key}");
        if (is_array($exact) && $exact !== []) {
            return array_values($exact);
        }

        $key = $this->section_key;
        foreach (config('website_sections.field_patterns', []) as $pattern => $fields) {
            if (fnmatch((string) $pattern, $key) && is_array($fields) && $fields !== []) {
                return array_values($fields);
            }
        }

        return ['title', 'subtitle', 'content', 'image', 'icon', 'link'];
    }

    public function showsField(string $field): bool
    {
        return in_array($field, $this->editableFields(), true);
    }

    public function isHiddenInAdmin(): bool
    {
        $needle = $this->page.'.'.$this->section_key;

        foreach (config('website_sections.hidden_sections', []) as $pattern) {
            if (fnmatch((string) $pattern, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function sectionsFor(string $page): \Illuminate\Support\Collection
    {
        return static::query()
            ->forWebsitePage($page)
            ->active()
            ->ordered()
            ->get()
            ->keyBy('section_key');
    }
}

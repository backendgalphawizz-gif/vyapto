<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteCareerItem extends Model
{
    public const CATEGORY_LIFE = 'life_at_vyapto';
    public const CATEGORY_DELIVERY_PARTNER = 'delivery_partner';
    public const CATEGORY_JOB_OPENING = 'job_opening';
    public const CATEGORY_NEWS = 'news_update';

    public const CATEGORIES = [
        self::CATEGORY_LIFE => 'Life at Vyapto',
        self::CATEGORY_DELIVERY_PARTNER => 'Join as Delivery Partner',
        self::CATEGORY_JOB_OPENING => 'Current Openings',
        self::CATEGORY_NEWS => 'News & Updates',
    ];

    protected $fillable = [
        'category',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'department',
        'location',
        'link',
        'published_at',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (WebsiteCareerItem $item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        return \App\Support\WebsiteMedia::url($this->image);
    }
}

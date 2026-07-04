<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteBlog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'author',
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
        static::creating(function (WebsiteBlog $blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('published_at')
                ->orWhere('published_at', '<=', now());
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('published_at')->orderBy('sort_order')->orderByDesc('id');
    }

    public function imageUrl(): ?string
    {
        return \App\Support\WebsiteMedia::url($this->image);
    }
}

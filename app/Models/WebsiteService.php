<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebsiteService extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'icon',
        'image',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

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

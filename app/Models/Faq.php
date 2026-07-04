<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $table = 'faqs';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'image',
        'image_url',
        'faq_category_id',
        'status',
        'sort_order'
    ];

    /**
     * Relationship: FAQ belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    /**
     * Scope: Only active FAQs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;

    protected $table = 'faq_categories';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    /**
     * Relationship: Category has many FAQs
     */
    public function faqs()
    {
        return $this->hasMany(Faq::class, 'faq_category_id');
    }
}
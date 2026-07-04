<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';
    public const STATUS_REPLIED = 'replied';

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_READ => 'Read',
        self::STATUS_REPLIED => 'Replied',
    ];
}

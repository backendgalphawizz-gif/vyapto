<?php
// app/Models/ParcelDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelDetail extends Model
{
    use HasFactory;

    protected $table = 'parcel_detail';

    protected $fillable = [
        'parcel_id',
        'assignment_parcel_id',
        'user_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updated(function (ParcelDetail $parcel) {
            if ($parcel->wasChanged('status') && $parcel->assignment_parcel_id) {
                $parcel->assignmentParcel?->syncStatusFromParcels();
            }
        });
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PACKED = 'packed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_PACKED => 'Packed',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    // Relationships
    public function assignmentParcel()
    {
        return $this->belongsTo(AssignmentParcel::class, 'assignment_parcel_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        // Backward-compatible alias if old calls still use seller().
        return $this->user();
    }

    // Accessor for status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Pending</span>',
            self::STATUS_ASSIGNED => '<span class="badge bg-info">Assigned</span>',
            self::STATUS_PROCESSING => '<span class="badge bg-info">Processing</span>',
            self::STATUS_PACKED => '<span class="badge bg-primary">Packed</span>',
            self::STATUS_SHIPPED => '<span class="badge bg-secondary">Shipped</span>',
            self::STATUS_DELIVERED => '<span class="badge bg-success">Delivered</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-danger">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}
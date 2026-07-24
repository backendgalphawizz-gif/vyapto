<?php
// app/Models/AssignmentParcel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentParcel extends Model
{
    use HasFactory;

    protected $table = 'assignment_parcel';

    protected $fillable = [
        'vendor_id',
        'vehicle_id',
        'user_id',
        'hub_id',
        'office_id',
        'parcel_quantity',
        'assignment_date',
        'from_date',
        'to_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'from_date' => 'date',
        'to_date' => 'date',
        'parcel_quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class, 'hub_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    // Accessor for status badge
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">Pending</span>',
            self::STATUS_ASSIGNED => '<span class="badge bg-info">Assigned</span>',
            self::STATUS_IN_TRANSIT => '<span class="badge bg-primary">In Transit</span>',
            self::STATUS_DELIVERED => '<span class="badge bg-success">Delivered</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-danger">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // Accessor for formatted quantity
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->parcel_quantity) . ' parcel(s)';
    }
    // app/Models/AssignmentParcel.php

    public function parcelDetails()
    {
        return $this->hasMany(ParcelDetail::class, 'assignment_parcel_id');
    }

    public function syncStatusFromParcels(): void
    {
        $statuses = $this->parcelDetails()->pluck('status');

        if ($statuses->isEmpty()) {
            return;
        }

        if ($statuses->every(fn ($status) => $status === ParcelDetail::STATUS_DELIVERED)) {
            if ($this->status !== self::STATUS_DELIVERED) {
                $this->update(['status' => self::STATUS_DELIVERED]);
            }
        }
    }
}

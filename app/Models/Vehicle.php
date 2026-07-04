<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'vehicle_number',
        'vehicle_type',
        'vendor_id',
        'rc_image',
        'insurance_image',
        'status'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}

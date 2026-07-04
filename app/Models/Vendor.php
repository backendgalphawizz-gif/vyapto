<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'business_name',
        'business_mobile',
        'email',
        'pan_number',
        'aadhar_number',
        'buisness_pan',
        'business_pan',
        'address',
        'city',
        'state',
        'latitude',
        'longitude',
        'gst_number',
        'gst_document',
        'profile_image',
        'cancelled_cheque_details',
        'cancelled_cheque_image',
        'bank_account_number',
        'bank_ifsc_code',
        'bank_account_image',
        'bank_statement_image',
        'status',
    ];
}

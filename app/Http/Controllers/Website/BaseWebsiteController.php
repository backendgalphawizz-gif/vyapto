<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Setting;

abstract class BaseWebsiteController extends Controller
{
    protected function sharedData(): array
    {
        return [
            'companyName' => Setting::where('type', 'company_name')->value('value') ?? 'Vyapto',
            'companyEmail' => Setting::where('type', 'company_email')->value('value') ?? 'support@vyapto.com',
            'companyPhone' => Setting::where('type', 'company_phone')->value('value') ?? '',
            'companyAddress' => Setting::where('type', 'company_address')->value('value') ?? '',
        ];
    }
}

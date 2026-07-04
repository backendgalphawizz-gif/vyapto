<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip — {{ date('F Y', mktime(0,0,0,$slip->month,1,$slip->year)) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            background: #eef3f8;
            color: #1f2937;
        }

        .page-actions {
            background: #0f766e;
            padding: 12px 22px;
        }
        .page-actions a, .page-actions button {
            background: #ffffff;
            color: #0f766e;
            border: none;
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .page-actions button { background: #0ea5a4; color: #fff; }

        .slip-wrapper {
            max-width: 900px;
            margin: 24px auto;
            background: #fff;
            border: 1px solid #d6e3ef;
            border-radius: 14px;
            overflow: hidden;
        }

        .company-header {
            padding: 14px 18px 8px;
            border-bottom: 2px solid #d6e3ef;
            background: #f8fbff;
        }
        .company-header p { font-size: 12px; line-height: 1.5; }

        .slip-title {
            font-weight: bold;
            font-size: 13px;
            padding: 8px 18px;
            border-bottom: 1px solid #d6e3ef;
            background: #f3f9ff;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .slip-title-text { flex: 1; min-width: 0; }
        .slip-company-logo {
            width: 52px;
            height: 52px;
            max-width: 52px;
            max-height: 52px;
            object-fit: contain;
            flex-shrink: 0;
            border-radius: 6px;
            background: #fff;
            border: 1px solid #e5edf6;
        }

        .emp-layout-table { width: 100%; border-collapse: collapse; border-bottom: 1px solid #d6e3ef; }
        .emp-layout-table td { vertical-align: top; }
        .emp-inner { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        .emp-inner td { padding: 2px 0; }
        .emp-inner td.emp-lbl { font-weight: bold; width: 140px; }

        .days-bar-table { width: 100%; border-collapse: collapse; background: #f6fbff; border-bottom: 1px solid #d6e3ef; font-size: 11.5px; }
        .days-bar-table td { padding: 5px 10px; }

        .slip-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #d6e3ef;
        }
        .slip-table th {
            background: #edf5ff;
            padding: 5px 8px;
            font-size: 11.5px;
            border: 1px solid #d6e3ef;
            text-align: center;
            text-transform: uppercase;
        }
        .slip-table td {
            padding: 4px 8px;
            border: 1px solid #e5edf6;
            font-size: 11.5px;
        }
        .slip-table td.amount { text-align: right; font-family: monospace; }
        .slip-table td.label-cell { font-weight: normal; }
        .slip-table tr.total-row td {
            font-weight: bold;
            background: #f8fbff;
            border-top: 1px solid #d6e3ef;
        }

        .col-divider { border-left: 1px solid #c9d9ea !important; }

        .net-pay-table { width: 100%; border-collapse: collapse; border-bottom: 1px solid #d6e3ef; background: #eef7ff; }
        .net-pay-table td { padding: 6px 18px; }
        .net-pay-table .net-label { font-weight: bold; font-size: 13px; }
        .net-pay-table .net-amount { font-weight: bold; font-size: 14px; font-family: monospace; text-align: right; }

        .net-words {
            padding: 4px 18px 8px;
            font-size: 11px;
            font-style: italic;
            border-bottom: 1px solid #d6e3ef;
            color: #444;
        }

        .tax-section { padding: 12px 18px 16px; background: #fcfeff; }
        .tax-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tax-table {
            width: 55%;
            border-collapse: collapse;
        }
        .tax-table td {
            padding: 3px 8px;
            font-size: 11.5px;
            border: 1px solid #e5edf6;
        }
        .tax-table td.t-amount {
            text-align: right;
            font-family: monospace;
            width: 80px;
        }

        .slip-footer-table { width: 100%; border-collapse: collapse; border-top: 1px solid #d6e3ef; font-size: 11px; color: #555; background: #f9fcff; }
        .slip-footer-table td { padding: 14px 18px; vertical-align: bottom; }

        @media print {
            body { background: #fff; }
            .page-actions { display: none; }
            .slip-wrapper { margin: 0; border: none; border-radius: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<!-- <div class="page-actions">
    <a href="{{ url('/') }}">← Back</a>
    <button type="button" onclick="window.print()">🖨 Print / Save PDF</button>
</div> -->

@php
    $emp = $slip->employee ?? $users ?? null;
    $monthStr = date('F Y', mktime(0,0,0,$slip->month,1,$slip->year));
    $salaryLabels = $salaryLabels ?? [
        'title_period' => 'MONTH',
        'col_base' => 'MONTHLY<br>SALARY',
        'col_current' => 'CURRENT<br>MONTH',
        'tax_curr' => 'CURR MONTH',
    ];

    $SettingClass = class_exists(\App\Models\Api\Setting::class)
        ? \App\Models\Api\Setting::class
        : \App\Models\Setting::class;

    function numberToWordsSlip(float $number): string {
        $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                 'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                 'Seventeen','Eighteen','Nineteen'];
        $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

        $n = (int) round($number);
        if ($n === 0) return 'Zero';

        $w = '';
        if ($n >= 10000000) { $w .= numberToWordsSlip($n / 10000000) . ' Crore '; $n %= 10000000; }
        if ($n >= 100000)   { $w .= numberToWordsSlip($n / 100000)   . ' Lakh ';  $n %= 100000; }
        if ($n >= 1000)     { $w .= numberToWordsSlip($n / 1000)     . ' Thousand '; $n %= 1000; }
        if ($n >= 100)      { $w .= $ones[(int)($n/100)] . ' Hundred '; $n %= 100; }
        if ($n >= 20)       { $w .= $tens[(int)($n/10)]; $n %= 10; if ($n) $w .= ' '; }
        if ($n > 0)         { $w .= $ones[$n]; }
        return trim($w);
    }

    $netWords = strtoupper(numberToWordsSlip((float) round($netPay))) . ' ONLY';

    $slipLogoUrl = $company_logo_url ?? null;
    if (!$slipLogoUrl) {
        $logoSetting = $SettingClass::where('type', 'company_web_logo')->first();
        if ($logoSetting && !empty($logoSetting->value)) {
            $slipLogoUrl = asset('storage/company/' . $logoSetting->value);
        }
    }
@endphp

<div class="slip-wrapper">

    <!-- <div class="company-header">
        @php
            $company = $SettingClass::where('type','company_name')->first();
            $address = $SettingClass::where('type','company_address')->first();
        @endphp
        <p><strong>{{ $company?->value ?? 'Vyapto Pvt Ltd' }}</strong></p>
        <p>{{ $address?->value ?? 'Embassy Tech Village, Outer Ring Road, Bengaluru – 560103, Karnataka, India' }}</p>
    </div> -->

    <div class="slip-title">
        <span class="slip-title-text">PAYSLIP FOR THE {{ $salaryLabels['title_period'] }} OF {{ strtoupper($monthStr) }}</span>
        @if(!empty($slipLogoUrl))
            <img src="{{ $slipLogoUrl }}" alt="" class="slip-company-logo" width="52" height="52" loading="lazy"
                 onerror="this.style.display='none'">
        @endif
    </div>

    <table class="emp-layout-table" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:50%; border-right:1px solid #e5edf6; padding:6px 18px;">
                <table class="emp-inner" cellspacing="0" cellpadding="0">
                    <tr><td class="emp-lbl">EMP CODE :</td><td>{{ str_pad((string)($emp?->id ?? 'N/A'), 6, '0', STR_PAD_LEFT) }}</td></tr>
                    <tr><td class="emp-lbl">EMP NAME :</td><td>{{ strtoupper($emp?->name ?? 'N/A') }}</td></tr>
                    <tr><td class="emp-lbl">DOB :</td><td>{{ $emp?->date_of_birth ? date('d/m/Y', strtotime($emp->date_of_birth)) : 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">GRP_DOJ :</td><td>{{ $emp?->join_date ? date('d/m/Y', strtotime($emp->join_date)) : 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">GENDER :</td><td>{{ $emp?->gender ? ucfirst($emp->gender) : 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">PAN NO :</td><td>{{ $emp?->pan_card_no ?? 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">AADHAR NO :</td><td>{{ $emp?->aadhar_card_no ?? 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">PHONE :</td><td>{{ $emp?->phone ?? 'N/A' }}</td></tr>
                </table>
            </td>
            <td style="width:50%; padding:6px 18px;">
                <table class="emp-inner" cellspacing="0" cellpadding="0">
                    <tr><td class="emp-lbl">BANK AC NO :</td><td>{{ $emp?->bank_account_no ?? 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">IFSC_CD :</td><td>{{ $emp?->ifsc_code ?? 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">BANK NAME :</td><td>{{ $emp?->bank_name ?? 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">BANK BRANCH :</td><td>{{ $emp?->bank_branch ?? 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">DOJ :</td><td>{{ $emp?->join_date ? date('d/m/Y', strtotime($emp->join_date)) : 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">JOB TYPE :</td><td>{{ $emp?->job_type ?? 'N/A' }}</td></tr>
                    <tr><td class="emp-lbl">DAYS WORKED :</td><td>{{ $slip->days_worked ?? '—' }}</td></tr>
                    <tr><td class="emp-lbl">EMAIL :</td><td>{{ $emp?->email ?? 'N/A' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="days-bar-table" cellspacing="0" cellpadding="0">
        <tr>
            <td><b>Standard Days:</b> {{ $slip->standard_days ?? '30' }}</td>
            <td><b>Payable Days:</b> {{ $slip->payable_days ?? '30.00' }}</td>
            <td><b>Loss of Pay Days:</b> {{ $slip->lop_days ?? '0.00' }}</td>
            <td><b>LOP Reversal Days:</b> {{ $slip->lop_reversal ?? '0.00' }}</td>
            <td><b>Arrear Days:</b> {{ $slip->arrear_days ?? '0.00' }}</td>
        </tr>
    </table>

    <table class="slip-table">
        <thead>
            <tr>
                <th style="width:28%">EARNINGS</th>
                <th style="width:14%">{!! $salaryLabels['col_base'] !!}</th>
                <th style="width:14%">{!! $salaryLabels['col_current'] !!}</th>
                <th style="width:10%">ARREAR<br>(+/-)</th>
                <th style="width:10%">TOTAL</th>
                <th class="col-divider" style="width:16%">DEDUCTIONS</th>
                <th style="width:8%">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label-cell">BASIC</td>
                <td class="amount">{{ number_format($earnings['basic'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['basic_current'] ?? $earnings['basic'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['basic_arrear'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['basic_total'] ?? $earnings['basic'] ?? 0, 0) }}</td>
                <td class="col-divider label-cell">P.T.</td>
                <td class="amount">{{ number_format($deductions['pt'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td class="label-cell">H.R.A</td>
                <td class="amount">{{ number_format($earnings['hra'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['hra_current'] ?? $earnings['hra'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['hra_arrear'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['hra_total'] ?? $earnings['hra'] ?? 0, 0) }}</td>
                <td class="col-divider"></td>
                <td class="amount"></td>
            </tr>
            <tr>
                <td class="label-cell">SPECIAL ALLOW</td>
                <td class="amount">{{ number_format($earnings['special_allow'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['special_current'] ?? $earnings['special_allow'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['special_arrear'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['special_total'] ?? $earnings['special_allow'] ?? 0, 0) }}</td>
                <td class="col-divider"></td>
                <td class="amount"></td>
            </tr>
            <tr>
                <td class="label-cell">STAT BONUS</td>
                <td class="amount">{{ number_format($earnings['stat_bonus'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['stat_current'] ?? $earnings['stat_bonus'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['stat_arrear'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['stat_total'] ?? $earnings['stat_bonus'] ?? 0, 0) }}</td>
                <td class="col-divider"></td>
                <td class="amount"></td>
            </tr>
            <tr class="total-row">
                <td>GROSS EARNINGS</td>
                <td class="amount">{{ number_format($earnings['monthly_gross'] ?? $earnings['gross'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['gross_current'] ?? $earnings['gross'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['gross_arrear'] ?? 0, 0) }}</td>
                <td class="amount">{{ number_format($earnings['gross'] ?? 0, 0) }}</td>
                <td class="col-divider">TOTAL DEDUCTIONS</td>
                <td class="amount">{{ number_format($deductions['total'] ?? 0, 0) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="net-pay-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="net-label">NET PAY</td>
            <td class="net-amount">{{ number_format($netPay, 0) }}</td>
        </tr>
    </table>
    <!-- <div class="net-words">({{ $netWords }})</div> -->

    <div class="tax-section">
        <div class="tax-title">Summary of Tax Computation as per New Regime</div>
        <table class="tax-table">
            <tr>
                <td>GROSS SALARY (EXCL. REIMBURSEMENT)</td>
                <td class="t-amount">{{ number_format($earnings['gross'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>ADD : PERQUISITES AND OTHER INCOME</td>
                <td class="t-amount">{{ number_format($earnings['perquisite'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>LESS : EXEMPT REIMBURSEMENT</td>
                <td class="t-amount">{{ number_format($taxLines['exempt_reimburse'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s 10</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_10'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s 16 (STD.DEDUCTION)</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_16'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s 24 (HOUSING LOSS)</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_24'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s CHAPTER VIA</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_via'] ?? 0, 0) }}</td>
            </tr>
            <tr style="font-weight:bold; background:#f5f5f5;">
                <td>NET TAXABLE INCOME</td>
                <td class="t-amount">{{ number_format($slip->net_taxable_income ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>TOTAL TAX PAYABLE</td>
                <td class="t-amount">{{ number_format($slip->total_tax_payable ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>TOTAL TAX RECOVERED (INCL. {{ $salaryLabels['tax_curr'] }})</td>
                <td class="t-amount">{{ number_format($slip->total_tax_recovered ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td>BALANCE TAX RECOVERABLE</td>
                <td class="t-amount">{{ number_format($slip->balance_tax_recoverable ?? 0, 0) }}</td>
            </tr>
        </table>
    </div>

    <table class="slip-footer-table" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width:45%;">
                <p>___________________________</p>
                <p><strong>Authorized Signature</strong></p>
            </td>
            <td style="text-align:right; color:#999; font-size:10px;">
                <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
                <p>Slip ID: #{{ $slip->slip_id ?? '—' }}</p>
            </td>
        </tr>
    </table>

</div>

</body>
</html>

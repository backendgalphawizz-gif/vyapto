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
            background: linear-gradient(135deg, #0f766e, #0ea5a4);
            padding: 12px 22px;
            display: flex;
            gap: 10px;
            align-items: center;
            box-shadow: 0 4px 16px rgba(2, 132, 199, 0.2);
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
        }
        .page-actions button { background: #0f766e; color: #fff; }

        .slip-wrapper {
            max-width: 900px;
            margin: 24px auto;
            background: #fff;
            border: 1px solid #d6e3ef;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.12);
        }

        /* Company Header */
        .company-header {
            padding: 14px 18px 8px;
            border-bottom: 2px solid #d6e3ef;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }
        .company-header p { font-size: 12px; line-height: 1.5; }

        /* Payslip Title + optional company logo (view / print) */
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

        /* Employee Info Grid */
        .emp-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #d6e3ef;
        }
        .emp-col { padding: 6px 18px; }
        .emp-col:first-child { border-right: 1px solid #e5edf6; }
        .emp-row {
            display: grid;
            grid-template-columns: 140px 1fr;
            padding: 2px 0;
            font-size: 11.5px;
        }
        .emp-row .label { font-weight: bold; }

        /* Days Bar */
        .days-bar {
            background: #f6fbff;
            padding: 5px 18px;
            font-size: 11.5px;
            border-bottom: 1px solid #d6e3ef;
            display: flex;
            gap: 30px;
        }
        .days-bar span b { margin-right: 4px; }

        /* Earnings / Deductions Table */
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
        .slip-table tr.spacer td { border: none; height: 4px; background: transparent; }

        /* Divider between earnings and deductions */
        .col-divider { border-left: 1px solid #c9d9ea !important; }

        /* NET PAY */
        .net-pay-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 18px;
            border-bottom: 1px solid #d6e3ef;
            background: linear-gradient(180deg, #f8fbff 0%, #eef7ff 100%);
        }
        .net-pay-row .net-label { font-weight: bold; font-size: 13px; }
        .net-pay-row .net-amount { font-weight: bold; font-size: 14px; font-family: monospace; }
        .net-words {
            padding: 4px 18px 8px;
            font-size: 11px;
            font-style: italic;
            border-bottom: 1px solid #d6e3ef;
            color: #444;
        }

        /* Tax Summary */
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

        /* Footer */
        .slip-footer {
            display: flex;
            justify-content: space-between;
            padding: 14px 18px;
            border-top: 1px solid #d6e3ef;
            font-size: 11px;
            color: #555;
            background: #f9fcff;
        }

        @media print {
            body { background: #fff; }
            .page-actions { display: none; }
            .slip-wrapper { margin: 0; border: none; border-radius: 0; max-width: 100%; box-shadow: none; }
        }
    </style>
</head>
<body>

{{-- Action Bar (hidden on print / PDF) --}}
@unless($forPdf ?? false)
<div class="page-actions">
    <a href="{{ route('salary-slips.index') }}">← Back to Salary Slips</a>
    <button onclick="window.print()">🖨 Print / Save PDF</button>
</div>
@endunless

@php
    $emp      = $slip->employee;
    $monthStr = date('F Y', mktime(0,0,0,$slip->month,1,$slip->year));
    $salaryLabels = $salaryLabels ?? [
        'title_period' => 'MONTH',
        'col_base' => 'MONTHLY<br>SALARY',
        'col_current' => 'CURRENT<br>MONTH',
        'tax_curr' => 'CURR MONTH',
    ];

    // ── Amount in words (simple) ──────────────────────────────────
    function numberToWords(float $number): string {
        $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                 'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                 'Seventeen','Eighteen','Nineteen'];
        $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

        $n = (int) round($number);
        if ($n === 0) return 'Zero';

        $w = '';
        if ($n >= 10000000) { $w .= numberToWords($n / 10000000) . ' Crore '; $n %= 10000000; }
        if ($n >= 100000)   { $w .= numberToWords($n / 100000)   . ' Lakh ';  $n %= 100000; }
        if ($n >= 1000)     { $w .= numberToWords($n / 1000)     . ' Thousand '; $n %= 1000; }
        if ($n >= 100)      { $w .= $ones[(int)($n/100)] . ' Hundred '; $n %= 100; }
        if ($n >= 20)       { $w .= $tens[(int)($n/10)]; $n %= 10; if ($n) $w .= ' '; }
        if ($n > 0)         { $w .= $ones[$n]; }
        return trim($w);
    }

    $netWords = strtoupper(numberToWords((float)$netPay)) . ' ONLY';

    $slipLogoUrl = $company_logo_url ?? null;
    if (!$slipLogoUrl && class_exists(\App\Models\Setting::class)) {
        $logoSetting = \App\Models\Setting::where('type', 'company_web_logo')->first();
        if ($logoSetting && !empty($logoSetting->value)) {
            $slipLogoUrl = asset('storage/company/' . $logoSetting->value);
        }
    }
@endphp

<div class="slip-wrapper">

    {{-- Company Header --}}
    <!-- <div class="company-header">
        @php
            $company = \App\Models\Setting::where('type','company_name')->first();
            $address = \App\Models\Setting::where('type','company_address')->first();
        @endphp
        <p><strong>{{ $company->value ?? 'Vyapto Pvt Ltd' }}</strong></p>
        <p>{{ $address->value ?? 'Embassy Tech Village, Outer Ring Road, Bengaluru – 560103, Karnataka, India' }}</p>
    </div> -->

    {{-- Payslip Title + company logo (small, right) --}}
    <div class="slip-title">
        <span class="slip-title-text">PAYSLIP FOR THE {{ $salaryLabels['title_period'] }} OF {{ strtoupper($monthStr) }}</span>
        @if(!empty($slipLogoUrl))
            <img src="{{ $slipLogoUrl }}" alt="" class="slip-company-logo" width="52" height="52" loading="lazy"
                 onerror="this.style.display='none'">
        @endif
    </div>

    {{-- Employee Info --}}
    <div class="emp-info">
        <div class="emp-col">
            <div class="emp-row"><span class="label">EMP CODE :</span><span>{{ str_pad($emp->id ?? 'N/A', 6, '0', STR_PAD_LEFT) }}</span></div>
            <div class="emp-row"><span class="label">EMP NAME :</span><span>{{ strtoupper($emp->name ?? 'N/A') }}</span></div>
            <div class="emp-row"><span class="label">DOB :</span><span>{{ $emp->date_of_birth ? date('d/m/Y', strtotime($emp->date_of_birth)) : 'N/A' }}</span></div>
            <!-- <div class="emp-row"><span class="label">GRP_DOJ :</span><span>{{ $emp->join_date ? date('d/m/Y', strtotime($emp->join_date)) : 'N/A' }}</span></div> -->
            <div class="emp-row"><span class="label">GENDER :</span><span>{{ $emp->gender ? ucfirst($emp->gender) : 'N/A' }}</span></div>
            <div class="emp-row"><span class="label">PAN NO :</span><span>{{ $emp->pan_card_no ?? 'N/A' }}</span></div>
            <div class="emp-row"><span class="label">AADHAR NO :</span><span>{{ $emp->aadhar_card_no ?? 'N/A' }}</span></div>
            <div class="emp-row"><span class="label">PHONE :</span><span>{{ $emp->phone ?? 'N/A' }}</span></div>
        </div>
        <div class="emp-col">
            <div class="emp-row"><span class="label">BANK AC NO :</span><span>{{ $emp->bank_account_no ?? 'N/A' }}</span></div>
            <div class="emp-row"><span class="label">IFSC_CD :</span><span>{{ $emp->ifsc_code ?? 'N/A' }}</span></div>
            <div class="emp-row"><span class="label">BANK NAME :</span><span>{{ $emp->bank_name ?? 'N/A' }}</span></div>
            <!-- <div class="emp-row"><span class="label">BANK BRANCH :</span><span>{{ $emp->bank_branch ?? 'N/A' }}</span></div> -->
            <!-- <div class="emp-row"><span class="label">DOJ :</span><span>{{ $emp->join_date ? date('d/m/Y', strtotime($emp->join_date)) : 'N/A' }}</span></div> -->
            <!-- <div class="emp-row"><span class="label">JOB TYPE :</span><span>{{ $emp->job_type ?? 'N/A' }}</span></div> -->
            <!-- <div class="emp-row"><span class="label">DAYS WORKED :</span><span>{{ $slip->days_worked ?? '—' }}</span></div> -->
            <div class="emp-row"><span class="label">EMAIL :</span><span>{{ $emp->email ?? 'N/A' }}</span></div>
        </div>
    </div>

    {{-- Days Bar --}}
    <div class="days-bar">
        <span><b>Standard Days:</b> {{ $slip->standard_days ?? '30' }}</span>
        <span><b>Payable Days:</b> {{ $slip->payable_days ?? '30.00' }}</span>
        <!-- <span><b>Loss of Pay Days:</b> {{ $slip->lop_days ?? '0.00' }}</span> -->
        <!-- <span><b>LOP Reversal Days:</b> {{ $slip->lop_reversal ?? '0.00' }}</span> -->
        <span><b>Arrear Days:</b> {{ $slip->arrear_days ?? '0.00' }}</span>
    </div>

    {{-- Earnings / Deductions Table --}}
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
            {{-- Row 1: BASIC | P.T. --}}
            <tr>
                <td class="label-cell">BASIC</td>
                <td class="amount">{{ number_format($earnings['basic'], 0) }}</td>
                <td class="amount">{{ number_format($earnings['basic'], 0) }}</td>
                <td class="amount"></td>
                <td class="amount">{{ number_format($earnings['basic'], 0) }}</td>
                <td class="col-divider label-cell">P.T.</td>
                <td class="amount">{{ number_format($deductions['pt'], 0) }}</td>
            </tr>
            {{-- Row 2: HRA --}}
            <tr>
                <td class="label-cell">H.R.A</td>
                <td class="amount">{{ number_format($earnings['hra'], 0) }}</td>
                <td class="amount">{{ number_format($earnings['hra'], 0) }}</td>
                <td class="amount"></td>
                <td class="amount">{{ number_format($earnings['hra'], 0) }}</td>
                <td class="col-divider"></td>
                <td class="amount"></td>
            </tr>
            {{-- Row 3: SPECIAL ALLOW --}}
            <tr>
                <td class="label-cell">SPECIAL ALLOW</td>
                <td class="amount">{{ number_format($earnings['special_allow'], 0) }}</td>
                <td class="amount">{{ number_format($earnings['special_allow'], 0) }}</td>
                <td class="amount"></td>
                <td class="amount">{{ number_format($earnings['special_allow'], 0) }}</td>
                <td class="col-divider"></td>
                <td class="amount"></td>
            </tr>
            {{-- Row 4: STAT BONUS --}}
            <tr>
                <td class="label-cell">STAT BONUS</td>
                <td class="amount">{{ number_format($earnings['stat_bonus'], 0) }}</td>
                <td class="amount">{{ number_format($earnings['stat_bonus'], 0) }}</td>
                <td class="amount"></td>
                <td class="amount">{{ number_format($earnings['stat_bonus'], 0) }}</td>
                <td class="col-divider"></td>
                <td class="amount"></td>
            </tr>
            {{-- Gross Earnings / Total Deductions --}}
            <tr class="total-row">
                <td>GROSS EARNINGS</td>
                <td class="amount">{{ number_format($earnings['gross'], 0) }}</td>
                <td class="amount">{{ number_format($earnings['gross'], 0) }}</td>
                <td class="amount"></td>
                <td class="amount">{{ number_format($earnings['gross'], 0) }}</td>
                <td class="col-divider">TOTAL DEDUCTIONS</td>
                <td class="amount">{{ number_format($deductions['total'], 0) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- NET PAY --}}
    <div class="net-pay-row">
        <div class="net-label">NET PAY</div>
        <div class="net-amount">{{ number_format($netPay, 0) }}</div>
    </div>
    <!-- <div class="net-words">({{ $netWords }})</div> -->

    {{-- Tax Computation Summary --}}
    <div class="tax-section">
        <div class="tax-title">Summary of Tax Computation as per New Regime</div>
        <table class="tax-table">
            <tr>
                <td>GROSS SALARY (EXCL. REIMBURSEMENT)</td>
                <td class="t-amount">{{ number_format($earnings['gross'], 0) }}</td>
            </tr>
            <tr>
                <td>ADD : PERQUISITES AND OTHER INCOME</td>
                <td class="t-amount">{{ number_format($earnings['perquisite'], 0) }}</td>
            </tr>
            <tr>
                <td>LESS : EXEMPT REIMBURSEMENT</td>
                <td class="t-amount">{{ number_format($taxLines['exempt_reimburse'], 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s 10</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_10'], 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s 16 (STD.DEDUCTION)</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_16'], 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s 24 (HOUSING LOSS)</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_24'], 0) }}</td>
            </tr>
            <tr>
                <td>LESS : DEDUCTION U/s CHAPTER VIA</td>
                <td class="t-amount">{{ number_format($taxLines['deduction_via'], 0) }}</td>
            </tr>
            <tr style="font-weight:bold; background:#f5f5f5;">
                <td>NET TAXABLE INCOME</td>
                <td class="t-amount">{{ number_format($slip->net_taxable_income, 0) }}</td>
            </tr>
            <tr>
                <td>TOTAL TAX PAYABLE</td>
                <td class="t-amount">{{ number_format($slip->total_tax_payable, 0) }}</td>
            </tr>
            <tr>
                <td>TOTAL TAX RECOVERED (INCL. {{ $salaryLabels['tax_curr'] }})</td>
                <td class="t-amount">{{ number_format($slip->total_tax_recovered, 0) }}</td>
            </tr>
            <tr>
                <td>BALANCE TAX RECOVERABLE</td>
                <td class="t-amount">{{ number_format($slip->balance_tax_recoverable, 0) }}</td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="slip-footer">
        <!-- <div>
            <p>___________________________</p>
            <p><strong>Authorized Signature</strong></p>
        </div> -->
        <div style="text-align:right; color:#999; font-size:10px;">
            <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
            <p>Slip ID: #{{ $slip->slip_id }}</p>
        </div>
    </div>

</div>

</body>
</html>

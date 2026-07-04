<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ExportsTabularData;

use Illuminate\Http\Request;

use App\Models\Api\PunchIn;
use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\User;





class AttendanceController extends Controller

{
    use ExportsTabularData;

    public function report(Request $request)

    {
        $monthInput = $request->input('month', now()->format('Y-m'));
        $employeeId = $request->input('employee_id');

        try {
            $monthStart = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Exception $e) {
            $monthStart = now()->startOfMonth();
        }

        $monthEnd = $monthStart->copy()->endOfMonth();

        $employeesQuery = User::where('status', 1)->where('role_id', '!=', 1);
        if ($employeeId) {
            $employeesQuery->where('id', $employeeId);
        }
        $employees = $employeesQuery->orderBy('name')->get();
        $employeeList = User::where('status', 1)->where('role_id', '!=', 1)->orderBy('name')->get();

        $companyStartTime = Setting::where('type', 'company_start_time')->value('value');
        $companyHalfTime = Setting::where('type', 'company_half_time')->value('value');

        $dateColumns = collect();
        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            $dateColumns->push($cursor->copy());
            $cursor->addDay();
        }

        $attendanceRows = Attendance::whereBetween('punch_in_date', [
                $monthStart->format('Y-m-d'),
                $monthEnd->format('Y-m-d')
            ])
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->groupBy(function ($item) {
                return $item->employee_id . '_' . $item->punch_in_date;
            });

        $reportRows = collect();

        foreach ($employees as $employee) {
            $dayStats = [];
            $present = 0;
            $late = 0;
            $halfDay = 0;
            $absent = 0;

            foreach ($dateColumns as $dateObj) {
                $dateString = $dateObj->format('Y-m-d');
                $key = $employee->id . '_' . $dateString;
                $attendance = optional($attendanceRows->get($key))->first();

                if (!$attendance) {
                    // Do not mark future calendar days as absent (day has not occurred yet).
                    if ($dateObj->copy()->startOfDay()->gt(Carbon::today())) {
                        $dayStats[] = [
                            'code' => 'U',
                            'label' => '—',
                            'class' => 'text-muted'
                        ];
                        continue;
                    }
                    $dayStats[] = [
                        'code' => 'A',
                        'label' => 'Absent',
                        'class' => 'bg-danger-subtle text-danger-emphasis'
                    ];
                    $absent++;
                    continue;
                }

                $statusCode = 'P';
                $statusLabel = 'Present';
                $statusClass = 'bg-success-subtle text-success-emphasis';

                if ($attendance->punch_in_time && $companyHalfTime) {
                    try {
                        $pIn = Carbon::parse($attendance->punch_in_time);
                        $halfLimit = Carbon::parse($dateString . ' ' . $companyHalfTime);
                        if ($pIn->gt($halfLimit)) {
                            $statusCode = 'H';
                            $statusLabel = 'Half Day';
                            $statusClass = 'bg-warning-subtle text-warning-emphasis';
                            $halfDay++;
                            $dayStats[] = ['code' => $statusCode, 'label' => $statusLabel, 'class' => $statusClass];
                            continue;
                        }
                    } catch (\Exception $e) {
                    }
                }

                if ($attendance->punch_in_time && $companyStartTime) {
                    try {
                        $pIn = Carbon::parse($attendance->punch_in_time);
                        $startLimit = Carbon::parse($dateString . ' ' . $companyStartTime);
                        if ($pIn->gt($startLimit)) {
                            $statusCode = 'L';
                            $statusLabel = 'Late';
                            $statusClass = 'bg-primary-subtle text-primary-emphasis';
                            $late++;
                        } else {
                            $present++;
                        }
                    } catch (\Exception $e) {
                        $present++;
                    }
                } else {
                    $present++;
                }

                $dayStats[] = [
                    'code' => $statusCode,
                    'label' => $statusLabel,
                    'class' => $statusClass
                ];
            }

            $reportRows->push([
                'employee' => $employee,
                'day_stats' => $dayStats,
                'summary' => [
                    'present' => $present,
                    'late' => $late,
                    'full_day' => $present + $late,
                    'half_day' => $halfDay,
                    'absent' => $absent,
                    'working_days' => $dateColumns->count()
                ]
            ]);
        }

        $selectedFilterDate = $request->input('filter_date');
        $selectedDayStatus = $request->input('day_status', '');
        $dayStatusLabels = [
            'present' => 'Present',
            'absent' => 'Absent',
            'half_day' => 'Half Day',
            'late' => 'Late',
        ];
        $dayFilterSummary = null;

        if ($selectedFilterDate && $selectedDayStatus !== '' && isset($dayStatusLabels[$selectedDayStatus])) {
            try {
                $filterDay = Carbon::parse($selectedFilterDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $filterDay = null;
            }
            if (
                $filterDay
                && $filterDay >= $monthStart->format('Y-m-d')
                && $filterDay <= $monthEnd->format('Y-m-d')
            ) {
                $wantLabel = $dayStatusLabels[$selectedDayStatus];
                $dayIdx = $dateColumns->search(fn ($d) => $d->format('Y-m-d') === $filterDay);
                if ($dayIdx !== false) {
                    $reportRows = $reportRows->filter(function ($row) use ($dayIdx, $wantLabel) {
                        return ($row['day_stats'][$dayIdx]['label'] ?? '') === $wantLabel;
                    })->values();
                    $dayFilterSummary = Carbon::parse($filterDay)->format('M j, Y') . ' — ' . $wantLabel;
                }
            }
        }

        if ($request->filled('format')) {
            $format = $this->exportFormat($request);

            return $this->exportAttendanceReport($format, $reportRows, $dateColumns, $monthStart);
        }

        return view('admin.attendance.report', [
            'reportRows' => $reportRows,
            'dateColumns' => $dateColumns,
            'employeeList' => $employeeList,
            'selectedMonth' => $monthStart->format('Y-m'),
            'selectedEmployee' => $employeeId,
            'selectedFilterDate' => $selectedFilterDate,
            'selectedDayStatus' => $selectedDayStatus,
            'monthDateMin' => $monthStart->format('Y-m-d'),
            'monthDateMax' => $monthEnd->format('Y-m-d'),
            'dayFilterSummary' => $dayFilterSummary,
        ]);
    }

    /**
     * Matrix export: one row per employee, one column per day + summary columns.
     */
    private function exportAttendanceReport(string $format, $reportRows, $dateColumns, Carbon $monthStart)
    {
        $headers = ['ID', 'Employee Name'];
        foreach ($dateColumns as $d) {
            $headers[] = $d->format('d M');
        }
        $headers = array_merge($headers, ['Present', 'Late', 'Full Day', 'Half Day', 'Absent']);

        $rows = [];
        foreach ($reportRows as $row) {
            $emp = $row['employee'];
            $s = $row['summary'];
            $line = [(string) $emp->id, (string) ($emp->name ?? '')];
            foreach ($row['day_stats'] as $ds) {
                $line[] = (string) ($ds['label'] ?? '');
            }
            $line[] = (string) $s['present'];
            $line[] = (string) $s['late'];
            $line[] = (string) $s['full_day'];
            $line[] = (string) $s['half_day'];
            $line[] = (string) $s['absent'];
            $rows[] = $line;
        }

        $basename = 'attendance_report_' . $monthStart->format('Y-m') . '_' . now()->format('Y-m-d_His');
        $title = 'Attendance Report — ' . $monthStart->format('F Y');

        if ($format === 'csv') {
            return $this->streamCsvDownload($basename, $headers, $rows);
        }
        if ($format === 'xlsx') {
            return $this->streamExcelTableDownload($basename, $title, $headers, $rows);
        }

        if (! class_exists(\Dompdf\Dompdf::class)) {
            return redirect()->back()->with('error', 'PDF library is not installed. Please install dompdf/dompdf.');
        }

        $html = '<html><head><meta charset="utf-8"><style>'
            . 'body{font-family:DejaVu Sans,Arial,sans-serif;font-size:7px;color:#111;}'
            . 'h2{font-size:14px;margin:0 0 8px 0;}'
            . 'table{border-collapse:collapse;width:100%;}'
            . 'th,td{border:1px solid #ccc;padding:2px 3px;text-align:left;}'
            . 'th{background:#f0f0f0;font-weight:bold;}'
            . '</style></head><body>'
            . '<h2>' . e($title) . '</h2>'
            . '<p style="font-size:9px;color:#555;">Exported at: ' . e(now()->format('d M Y h:i A')) . '</p>'
            . '<table><thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . e($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $line) {
            $html .= '<tr>';
            foreach ($line as $cell) {
                $html .= '<td>' . e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }
        if (count($rows) === 0) {
            $html .= '<tr><td colspan="' . count($headers) . '" style="text-align:center;">No data</td></tr>';
        }
        $html .= '</tbody></table></body></html>';

        $dompdf = new \Dompdf\Dompdf([
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        $filename = $basename . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function filter(Request $request) 

    {

        $filters = $request->only(['from_date', 'to_date', 'employee_id', 'status', 'exception']);

        session(['attendance_filters' => $filters]);



        return redirect()->route('attendance.index', ['filter' => 1]);

    }



    public function index(Request $request)
    {
        if (!$request->has('filter')) {
            session()->forget('attendance_filters');
            $filters = [];
        } else {
            $filters = session('attendance_filters', []);
        }

        $fromDate = $filters['from_date'] ?? now()->format('Y-m-d');
        $toDate = $filters['to_date'] ?? now()->format('Y-m-d');
        
        $filterEmployeeId = $filters['employee_id'] ?? null;
        $filterStatus = $filters['status'] ?? null;
        $filterException = $filters['exception'] ?? null;

        $employeesQuery = User::where('status', 1)->where('role_id', '!=', 1); 

        if ($filterEmployeeId) {
            $employeesQuery->where('id', $filterEmployeeId);
        }
        
        $activeEmployees = $employeesQuery->orderBy('name')->get();
        $employeeList = User::where('status', 1)->where('role_id', '!=', 1)->orderBy('name')->get();

        $period = \Carbon\CarbonPeriod::create($fromDate, $toDate);

        // Fetch Company Settings
        $companyStartTime = Setting::where('type', 'company_start_time')->value('value');
        $companyEndTime   = Setting::where('type', 'company_end_time')->value('value');
        $companyHalfTime  = Setting::where('type', 'company_half_time')->value('value');

        // Parse once to save cycles (using arbitrary date, we only care about time component)
        // Note: Carbon::parse will use today's date if only time passed, which is fine for comparison if we standardise
        $reportData = collect();

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $dayAttendances = PunchIn::where('punch_in_date', $dateString)
                                        ->get()
                                        ->keyBy('employee_id');

            foreach ($activeEmployees as $employee) {
                $attendance = $dayAttendances->get($employee->id);
                
                $status = $attendance ? 'Present' : 'Absent';
                $exception = '';
                
                $punchIn = null;
                $punchOut = null;
                $punchInLoc = null;
                $punchOutLoc = null;

                if ($attendance) {
                     $punchIn = $attendance->punch_in_time;
                     $punchOut = $attendance->punch_out_time;
                     $punchInLoc = $attendance->punch_in_location;
                     $punchOutLoc = $attendance->punch_out_location;

                     // Calculate Status & Exceptions based on Time if settings exist
                     if ($punchIn && $companyStartTime) {
                         try {
                             $pIn = Carbon::parse($punchIn);
                             // Create comparison time on the same date as the punch-in
                             $sTime = Carbon::parse($dateString . ' ' . $companyStartTime);
                             
                             if ($companyHalfTime) {
                                 $hTime = Carbon::parse($dateString . ' ' . $companyHalfTime);
                                 if ($pIn->gt($hTime)) {
                                     $status = 'Half Day';
                                 } elseif ($pIn->gt($sTime)) {
                                     $exception .= 'Late arrival ';
                                 }
                             } elseif ($pIn->gt($sTime)) {
                                  $exception .= 'Late arrival ';
                             }
                         } catch (\Exception $e) {}
                     }
                     
                     if ($punchOut && $companyEndTime) {
                         try {
                             $pOut = Carbon::parse($punchOut);
                             $eTime = Carbon::parse($dateString . ' ' . $companyEndTime);
                             
                             if ($pOut->lt($eTime)) {
                                 $exception .= 'Early leave ';
                             }
                         } catch (\Exception $e) {}
                     }
                     
                     // Append manual exceptions if they exist in DB and aren't already added
                     $manualInExc = $attendance->punch_in_exception;
                     $manualOutExc = $attendance->punch_out_exception;
                     
                     if ($manualInExc && !str_contains($exception, $manualInExc)) $exception .= $manualInExc . ' ';
                     if ($manualOutExc && !str_contains($exception, $manualOutExc)) $exception .= $manualOutExc . ' ';
                     
                     $exception = trim($exception) ?: '-';
                }

                // Apply Filters
                if ($filterStatus) {
                    if ($filterStatus == 'Present' && $status == 'Absent') continue;
                    if ($filterStatus == 'Absent' && $status != 'Absent') continue; 
                    if ($filterStatus == 'Half Day' && $status != 'Half Day') continue;
                }

                if ($filterException) {
                     if (!$attendance) continue;
                     if (!str_contains($exception, $filterException)) continue;
                }

                $reportData->push([
                    'date' => $dateString,
                    'employee' => $employee,
                    'status' => $status,
                    'punch_in' => $punchIn,
                    'punch_out' => $punchOut,
                    'location' => ($punchInLoc ?: $punchOutLoc) ?: '-',
                    'punch_in_location' => $punchInLoc,
                    'punch_out_location' => $punchOutLoc,
                    'exception' => $exception,
                    'punch_in_exception' => $attendance ? $attendance->punch_in_exception : null,
                    'punch_out_exception' => $attendance ? $attendance->punch_out_exception : null,
                    'id' => $attendance ? $attendance->id : null
                ]);
            }
        }
        
        $sortBy = $request->input('sort_by', 'date');
        $sortOrder = $request->input('sort_order', 'desc');

        $sortedData = $reportData->sortBy(function ($row, $key) use ($sortBy) {
            if ($sortBy === 'employee') return $row['employee']->name;
            if ($sortBy === 'punch_in' || $sortBy === 'punch_out') {
                $val = $row[$sortBy];
                if ($val instanceof \Carbon\Carbon) return $val->timestamp;
                return $val ? strtotime($val) : null;
            }
            return $row[$sortBy] ?? null;
        }, SORT_REGULAR, $sortOrder === 'desc');

        $totalPresent = $sortedData->where('status', 'Present')->count();
        $totalAbsent = $sortedData->where('status', 'Absent')->count();
        $totalLate = $sortedData->filter(function ($item) {
            return str_contains($item['exception'], 'Late arrival');
        })->count();
        $totalEarly = $sortedData->filter(function ($item) {
            return str_contains($item['exception'], 'Early leave');
        })->count();

        $page = $request->input('page', 1);
        $perPage = 10;
        $activePageData = $sortedData->forPage($page, $perPage); 

        $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $activePageData,
            $sortedData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.attendance.index', compact(
            'attendances', 
            'employeeList', 
            'fromDate', 
            'toDate',
            'filterEmployeeId',
            'filterStatus',
            'filterException',
            'totalPresent',
            'totalAbsent',
            'totalLate',
            'totalEarly'
        ));
    }




    public function store(Request $request)

    {

        $request->validate([

            'employee_id' => 'required|exists:users,id',

            'date' => 'required|date',

            'status' => 'required|in:Present,Absent',

            'punch_in_time' => 'nullable|required_if:status,Present',

            'punch_out_time' => 'nullable',

            'location' => 'nullable|string|max:255',
            
            'punch_in_location' => 'nullable|string|max:255',

            'punch_out_location' => 'nullable|string|max:255',

            'exception' => 'nullable|string|max:255',

        ]);




        if ($request->status === 'Absent') {
             $existing = PunchIn::where('employee_id', $request->employee_id)
                                ->where('punch_in_date', $request->date)
                                ->first();
                                
             if ($existing) {
                 $existing->delete(); 
             }

             if ($request->ajax()) {
                return response()->json(['success' => 'Attendance marked as Absent.']);
             }
             return redirect()->route('attendance.index')->with('success', 'Attendance marked as Absent.');
        }

        $punchInTime = $request->punch_in_time ? Carbon::parse($request->date . ' ' . $request->punch_in_time) : null;
        $punchOutTime = $request->punch_out_time ? Carbon::parse($request->date . ' ' . $request->punch_out_time) : null;

        PunchIn::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'punch_in_date' => $request->date,
            ],
            [
                'punch_in_time' => $punchInTime,
                'punch_out_date' => $request->date, 
                'punch_out_time' => $punchOutTime,
                'punch_in_location' => $request->punch_in_location,
                'punch_out_location' => $request->punch_out_location,
                'punch_in_exception' => $request->exception,
                'punch_in_lat' => '0.0', 
                'punch_in_long' => '0.0', 
                'punch_out_lat' => '0.0',
                'punch_out_long' => '0.0',
            ]

        );




        if ($request->ajax()) {

            return response()->json(['success' => 'Attendance created successfully.']);

        }



        return redirect()->route('attendance.index')

            ->with('success', 'Attendance created successfully.');

    }




    public function update(Request $request, $id)

    {

        $attendance = PunchIn::findOrFail($id);



        $request->validate([

            'employee_id' => 'required|exists:users,id',

            'date' => 'required|date',

            'status' => 'required|in:Present,Absent',

            'punch_in_time' => 'nullable|required_if:status,Present',

            'punch_out_time' => 'nullable',

            'location' => 'nullable|string|max:255',
            
            'punch_in_location' => 'nullable|string|max:255',

            'punch_out_location' => 'nullable|string|max:255',

            'exception' => 'nullable|string|max:255',

        ]);




        if ($request->status === 'Absent') {

            $attendance->delete();



            if ($request->ajax()) {

                return response()->json(['success' => 'Attendance marked as Absent.']);

            }

            return redirect()->route('attendance.index')->with('success', 'Attendance marked as Absent.');

        }



        $punchInTime = $request->punch_in_time ? Carbon::parse($request->date . ' ' . $request->punch_in_time) : null;

        $punchOutTime = $request->punch_out_time ? Carbon::parse($request->date . ' ' . $request->punch_out_time) : null;



        $attendance->update([
           'punch_in_time' => $punchInTime,
           'punch_in_location' => $request->punch_in_location,
           'punch_in_exception' => $request->exception,
           'punch_in_lat' => '0.0', 
           'punch_in_long' => '0.0', 
           'punch_out_lat' => '0.0',
           'punch_out_long' => '0.0',
           'punch_out_time' => $punchOutTime,
           'punch_out_date' => $request->date, 
           'punch_out_location' => $request->punch_out_location,
        ]);





        if ($request->ajax()) {

            return response()->json(['success' => 'Attendance updated successfully.']);

        }



        return redirect()->route('attendance.index')

            ->with('success', 'Attendance updated successfully.');

    }



    public function destroy($id)
    {
        $attendance = PunchIn::findOrFail($id);
        $attendance->delete();


        return redirect()->route('attendance.index')

            ->with('success', 'Attendance deleted successfully.');

    }


}


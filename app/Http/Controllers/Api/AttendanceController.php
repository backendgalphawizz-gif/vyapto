<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Api\PunchIn;
use App\Models\Api\Salary;
use App\Models\Api\UserToken;
use App\Models\Api\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Validator;
use Auth;
use DB;

class AttendanceController extends Controller
{
    public function attendanceRecord(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'employee_id' => 'required',
			'type'        => 'required',
			'from_date'   => 'nullable|date',
			'to_date'     => 'nullable|date|after_or_equal:from_date',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$token = str_replace('Bearer ', '', $request->header('Authorization'));
		if (!empty($token)) {
			$userToken = UserToken::where('token', $token)->first();
			if (!$userToken) {
				return response()->json([
					'status' => false,
					'message' => 'Invalid or expired token'
				], 401);
			}
		}
		
		$user = auth('api')->user();
		
		$query = PunchIn::where('employee_id', $user->id);

		if (!empty($request->from_date) && !empty($request->to_date)) {

			$query->whereBetween('punch_in_date', [
				$request->from_date,
				$request->to_date
			]);

			$punch_details = $query->get();
		} else {

			if ($request->type == 'today') {
				$today = date('Y-m-d');
				$punch_details = $query->whereDate('punch_in_date', $today)->first();
			} else {
				$currentMonth = date('m');
				$currentYear  = date('Y');

				$punch_details = $query
					->whereMonth('punch_in_date', $currentMonth)
					->whereYear('punch_in_date', $currentYear)
					->get();
			}
		}

		return response()->json([
			'status'  => true,
			'code'    => 200,
			'message' => 'Punch In And Punch Out Details',
			'data'    => $punch_details,
		]);
	}
		
		
	/*public function salaryRecord(Request $request)
	{

        $validator = Validator::make($request->all(), [
			'employee_id' => 'required',
			'year'        => 'required|digits:4', // year required
		]);
		
		
		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$token = str_replace('Bearer ', '', $request->header('Authorization'));
		if (!empty($token)) {
			$userToken = UserToken::where('token', $token)->first();
			if (!$userToken) {
				return response()->json([
					'status' => false,
					'message' => 'Invalid or expired token'
				], 401);
			}
		}
		
		$user = auth('api')->user();
		$salarySlipUrl = url('/api/salary-slip/'.$user->id);
		$result = Salary::where('employee_id', $user->id)->first();
		if(!empty($result)){
			return response()->json([
				'status'  => true,
				'code'    => 200,
				'message' => 'Salary Details',
				'data'    => $result,
				'salary_slip_url' => $salarySlipUrl, 
			]);
		}else{
			return response()->json([
				'status'  => false,
				'code'    => 200,
				'message' => 'No Record',
			]);
		}
	}*/
	
	public function salaryRecord(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'employee_id' => 'required',
			'year'        => 'required|digits:4',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$user = auth('api')->user();

		$salaryQuery = Salary::where('employee_id', $user->id);
		$this->applySalaryYearWhere($salaryQuery, (int) $request->year);
		$this->applySalaryPeriodOrdering($salaryQuery);

		$salaryData = $salaryQuery->get()->map(function ($salary) {
				[$y, $m] = $this->resolveSlipYearMonth($salary);
				$slipDate = sprintf('%04d-%02d-01', $y, $m);
				$salary->salary_slip_url = url('/api/salary-slip-pdf/'.$salary->employee_id.'/'.$slipDate);

				$bd = $this->salarySlipBreakdown($salary);
				$gross = (float) ($bd['earnings']['gross'] ?? 0);
				$ded = (float) ($bd['deductions']['total'] ?? 0);

				$payload = $salary->toArray();
				$payload['payslip'] = [
					'earnings'    => $bd['earnings'],
					'deductions'  => $bd['deductions'],
					'tax_lines'   => $bd['taxLines'],
					'net_pay'     => max(0, $gross - $ded),
					'gross_total' => $gross,
				];

				return $payload;
			});

		if ($salaryData->count() > 0) {

			return response()->json([
				'status'  => true,
				'code'    => 200,
				'message' => 'Salary Details Year Wise',
				'year'    => $request->year,
				'data'    => $salaryData,
			]);
		} else {

			return response()->json([
				'status'  => false,
				'code'    => 200,
				'message' => 'No Record Found For This Year',
			]);
		}
	}

    public function salarySlipView($employee_id, $date)
	{
		$salary = $this->findSalarySlipRow($employee_id, $date);
		$users = DB::table('users')->where('id', $employee_id)->first();

		if (!$salary) {
			return response()->json([
				'status'  => false,
				'code'    => 404,
				'message' => 'Salary record not found',
			]);
		}

		return view('salary.slip', $this->salarySlipViewVars($salary, $users));
	}

	public function salarySlipPdf($employee_id, $date)
	{
		$salary = $this->findSalarySlipRow($employee_id, $date);
		$users = DB::table('users')->where('id', $employee_id)->first();

		if (!$salary) {
			return response()->json([
				'status'  => false,
				'code'    => 404,
				'message' => 'Salary record not found',
			], 404);
		}

		$pdf = Pdf::loadView('salary.slip', $this->salarySlipViewVars($salary, $users));

		$filename = 'salary-slip-'.$employee_id.'-'.$date.'.pdf';

		return $pdf->stream($filename);
	}

	/**
	 * Variables for salary slip Blade (admin + app PDF use same row / JSON).
	 */
	protected function salarySlipViewVars(Salary $salary, $users): array
	{
		$slip = $salary;
		$breakdown = $this->salarySlipBreakdown($slip);
		// Always derive net pay from the same breakdown shown on the slip (admin panel behaviour).
		// Stale columns like total_salary / net_salary on the row caused wrong figures (e.g. 33,000 vs 57,000).
		$gross = (float) ($breakdown['earnings']['gross'] ?? 0);
		$deduct = (float) ($breakdown['deductions']['total'] ?? 0);
		$netPay = max(0, $gross - $deduct);

		$salaryLabels = $this->resolveSalaryPeriodLabels($slip);

		return [
			'salary'       => $salary,
			'users'        => $users,
			'slip'         => $slip,
			'netPay'       => $netPay,
			'earnings'     => $breakdown['earnings'],
			'deductions'   => $breakdown['deductions'],
			'taxLines'     => $breakdown['taxLines'],
			'salaryLabels' => $salaryLabels,
			'salaryType'   => $salaryLabels['type'],
		];
	}

	/**
	 * Labels for payslip title / columns (monthly vs weekly vs daily) — matches admin UserSalary.salary_type.
	 */
	protected function resolveSalaryPeriodLabels($slip): array
	{
		$type = 'monthly';
		$uid = $this->slipEmployeeId($slip);
		if ($uid && class_exists(\App\Models\UserSalary::class)) {
			$us = \App\Models\UserSalary::where('user_id', $uid)->first();
			if ($us && ! empty($us->salary_type)) {
				$type = $us->salary_type;
			}
		}

		return match ($type) {
			'weekly' => [
				'type' => 'weekly',
				'title_period' => 'WEEK',
				'col_base' => 'WEEKLY<br>SALARY',
				'col_current' => 'CURRENT<br>WEEK',
				'tax_curr' => 'CURR WEEK',
			],
			'daily' => [
				'type' => 'daily',
				'title_period' => 'DAY',
				'col_base' => 'DAILY<br>SALARY',
				'col_current' => 'CURRENT<br>DAY',
				'tax_curr' => 'CURR DAY',
			],
			default => [
				'type' => 'monthly',
				'title_period' => 'MONTH',
				'col_base' => 'MONTHLY<br>SALARY',
				'col_current' => 'CURRENT<br>MONTH',
				'tax_curr' => 'CURR MONTH',
			],
		};
	}

	/**
	 * Latest basic from User Salary management when present (same source as admin payslip).
	 */
	protected function resolveSlipBasic($slip): float
	{
		$uid = $this->slipEmployeeId($slip);
		if ($uid && class_exists(\App\Models\UserSalary::class)) {
			$us = \App\Models\UserSalary::where('user_id', $uid)->first();
			if ($us && $us->salary_amount !== null && $us->salary_amount !== '') {
				return (float) $us->salary_amount;
			}
		}

		return $this->firstNumericAttribute($slip, [
			'basic_salary', 'basic', 'basic_pay', 'basic_amount', 'basic_sal',
		]);
	}

	/**
	 * Match slip row by period. Supports year/month columns, alternate names, or a single date column.
	 */
	protected function findSalarySlipRow($employeeId, string $date): ?Salary
	{
		$c = Carbon::parse($date);
		$builder = Salary::where('employee_id', $employeeId);
		$this->applySalaryPeriodForMonthWhere($builder, $c);
		$row = $builder->first();
		if ($row) {
			return $row;
		}

		if ($this->salaryColumnExists('date')) {
			$row = Salary::where('employee_id', $employeeId)
				->whereDate('date', $c->toDateString())
				->first();
			if ($row) {
				return $row;
			}
		}

		// API `salaries` row may be missing while admin `salary_slips` exists — hydrate from DB for PDF / breakdown.
		$dbRow = $this->fetchSalarySlipDatabaseRow($employeeId, (int) $c->year, (int) $c->month);
		if ($dbRow !== []) {
			$api = new Salary;
			$api->forceFill($dbRow);
			$api->exists = true;
			if (isset($dbRow['id'])) {
				$api->setAttribute($api->getKeyName(), $dbRow['id']);
			}

			return $api;
		}

		return null;
	}

	/**
	 * Load the same row the admin panel uses (salary_slips). Does not require App\Models\SalarySlip to exist.
	 *
	 * @return array<string, mixed>
	 */
	protected function fetchSalarySlipDatabaseRow($employeeId, int $year, int $month): array
	{
		if (! Schema::hasTable('salary_slips')) {
			return [];
		}

		// Prefer raw salary_slips table — Eloquent SalarySlip may point at a different table in some apps.
		foreach (['employee_id', 'user_id'] as $col) {
			if (! Schema::hasColumn('salary_slips', $col)) {
				continue;
			}
			$row = DB::table('salary_slips')
				->where($col, $employeeId)
				->where('year', $year)
				->where('month', $month)
				->first();
			if ($row) {
				return (array) $row;
			}
		}

		if (class_exists(\App\Models\SalarySlip::class)) {
			$admin = \App\Models\SalarySlip::query()
				->where('employee_id', $employeeId)
				->where('year', $year)
				->where('month', $month)
				->first();
			if ($admin) {
				return $admin->getAttributes();
			}
		}

		return [];
	}

	/**
	 * Employee id for payslip (column is usually employee_id; some rows use user_id).
	 */
	protected function slipEmployeeId($slip)
	{
		if (is_object($slip)) {
			return $slip->employee_id ?? $slip->user_id ?? null;
		}

		return null;
	}

	/**
	 * Resolve year/month for salary_slips lookup from whatever the API model stores.
	 *
	 * @return array{employee_id: int|string, year: int, month: int}|null
	 */
	protected function resolveSlipPeriodFromModel($slip): ?array
	{
		$eid = $this->slipEmployeeId($slip);
		if ($eid === null || $eid === '') {
			return null;
		}

		foreach ([['year', 'month'], ['salary_year', 'salary_month'], ['pay_year', 'pay_month'], ['slip_year', 'slip_month']] as [$yc, $mc]) {
			if (! isset($slip->{$yc}, $slip->{$mc})) {
				continue;
			}
			if ($slip->{$yc} === null || $slip->{$mc} === null || $slip->{$yc} === '' || $slip->{$mc} === '') {
				continue;
			}

			return [
				'employee_id' => $eid,
				'year' => (int) $slip->{$yc},
				'month' => (int) $slip->{$mc},
			];
		}

		if (isset($slip->date) && $slip->date) {
			try {
				$cd = Carbon::parse($slip->date);

				return [
					'employee_id' => $eid,
					'year' => (int) $cd->year,
					'month' => (int) $cd->month,
				];
			} catch (\Exception $e) {
			}
		}

		return null;
	}

	/**
	 * Copy canonical salary_slips columns onto the in-memory model so getAttributes() sees HRA / special_allow / types.
	 * Merging only into slipScalarData()'s temp array is not enough when code reads $slip->hra_value directly.
	 */
	protected function hydrateSlipFromSalarySlipsTable($slip): void
	{
		if (! ($slip instanceof \Illuminate\Database\Eloquent\Model)) {
			return;
		}

		$period = $this->resolveSlipPeriodFromModel($slip);
		if ($period === null) {
			return;
		}

		$row = $this->fetchSalarySlipDatabaseRow($period['employee_id'], $period['year'], $period['month']);
		if ($row === []) {
			return;
		}

		foreach ($row as $key => $val) {
			if ($val === null || $val === '') {
				continue;
			}
			$keyStr = (string) $key;
			if (preg_match('/_(value|type)$/', $keyStr)) {
				$slip->setAttribute($keyStr, $val);

				continue;
			}
			if (in_array($keyStr, [
				'basic_salary',
				'net_taxable_income',
				'total_tax_payable',
				'total_tax_recovered',
				'balance_tax_recoverable',
			], true)) {
				$slip->setAttribute($keyStr, $val);
			}
		}
	}

	/** @var array<string, bool> */
	protected static $salaryColumnCache = [];

	protected function salaryTable(): string
	{
		return (new Salary)->getTable();
	}

	protected function salaryColumnExists(string $column): bool
	{
		$table = $this->salaryTable();
		$key = $table.'.'.$column;
		if (! array_key_exists($key, static::$salaryColumnCache)) {
			static::$salaryColumnCache[$key] = Schema::hasColumn($table, $column);
		}

		return static::$salaryColumnCache[$key];
	}

	protected function applySalaryPeriodForMonthWhere($query, Carbon $c): void
	{
		if ($this->salaryColumnExists('year') && $this->salaryColumnExists('month')) {
			$query->where('year', $c->year)->where('month', $c->month);

			return;
		}

		$pairs = [
			['salary_year', 'salary_month'],
			['pay_year', 'pay_month'],
			['slip_year', 'slip_month'],
		];
		foreach ($pairs as [$yCol, $mCol]) {
			if ($this->salaryColumnExists($yCol) && $this->salaryColumnExists($mCol)) {
				$query->where($yCol, $c->year)->where($mCol, $c->month);

				return;
			}
		}

		if ($this->salaryColumnExists('date')) {
			$query->whereYear('date', $c->year)->whereMonth('date', $c->month);

			return;
		}

		$query->whereRaw('1 = 0');
	}

	protected function applySalaryYearWhere($query, int $year): void
	{
		if ($this->salaryColumnExists('year')) {
			$query->where('year', $year);

			return;
		}

		foreach (['salary_year', 'pay_year', 'slip_year'] as $col) {
			if ($this->salaryColumnExists($col)) {
				$query->where($col, $year);

				return;
			}
		}

		if ($this->salaryColumnExists('date')) {
			$query->whereYear('date', $year);

			return;
		}

		$query->whereRaw('1 = 0');
	}

	protected function applySalaryPeriodOrdering($query): void
	{
		if ($this->salaryColumnExists('year') && $this->salaryColumnExists('month')) {
			$query->orderBy('month', 'asc');

			return;
		}

		if ($this->salaryColumnExists('salary_year') && $this->salaryColumnExists('salary_month')) {
			$query->orderBy('salary_month', 'asc');

			return;
		}

		if ($this->salaryColumnExists('pay_year') && $this->salaryColumnExists('pay_month')) {
			$query->orderBy('pay_month', 'asc');

			return;
		}

		if ($this->salaryColumnExists('slip_year') && $this->salaryColumnExists('slip_month')) {
			$query->orderBy('slip_month', 'asc');

			return;
		}

		if ($this->salaryColumnExists('date')) {
			$query->orderBy('date', 'asc');

			return;
		}

		$query->orderBy('id', 'asc');
	}

	/**
	 * @return array{0: int, 1: int}
	 */
	protected function resolveSlipYearMonth($salary): array
	{
		if ($this->salaryColumnExists('year') && $this->salaryColumnExists('month')
			&& $salary->year !== null && $salary->month !== null && $salary->year !== '' && $salary->month !== '') {
			return [(int) $salary->year, (int) $salary->month];
		}

		$pairs = [
			['salary_year', 'salary_month'],
			['pay_year', 'pay_month'],
			['slip_year', 'slip_month'],
		];
		foreach ($pairs as [$yCol, $mCol]) {
			if ($this->salaryColumnExists($yCol) && $this->salaryColumnExists($mCol)) {
				$y = $salary->{$yCol} ?? null;
				$m = $salary->{$mCol} ?? null;
				if ($y !== null && $m !== null && $y !== '' && $m !== '') {
					return [(int) $y, (int) $m];
				}
			}
		}

		if ($this->salaryColumnExists('date') && ! empty($salary->date)) {
			$cd = Carbon::parse($salary->date);

			return [(int) $cd->year, (int) $cd->month];
		}

		return [(int) date('Y'), 1];
	}

	/**
	 * Flatten model attributes + optional JSON columns (admin sometimes stores breakdown in one JSON field).
	 */
	protected function slipScalarData($slip): array
	{
		$attrs = $slip instanceof \Illuminate\Database\Eloquent\Model
			? $slip->getAttributes()
			: (array) $slip;

		$jsonColumns = [
			'payload', 'meta', 'details', 'components', 'breakdown',
			'salary_data', 'extra_data', 'payslip_data', 'data',
		];

		foreach ($jsonColumns as $col) {
			if (empty($attrs[$col])) {
				continue;
			}
			$raw = $attrs[$col];
			if (is_string($raw)) {
				$dec = json_decode($raw, true);
			} elseif (is_array($raw)) {
				$dec = $raw;
			} else {
				continue;
			}
			if (! is_array($dec)) {
				continue;
			}
			$this->mergeDecodedSalaryIntoAttrs($attrs, $dec);
		}

		// Admin panel stores full component rows on salary_slips; API `salaries` rows are often minimal.
		// Overlay admin columns so HRA / special allow / stat bonus % + type resolve like the admin slip.
		$this->mergeAdminSalarySlipAttributes($attrs, $slip);

		$this->applySalaryComponentAliases($attrs);

		return $attrs;
	}

	/**
	 * Overlay canonical payslip components from salary_slips (same as admin). API rows often omit *_value / *_type.
	 * Uses DB when App\Models\SalarySlip is not registered in the API app.
	 */
	protected function mergeAdminSalarySlipAttributes(array &$attrs, $slip): void
	{
		$employeeId = $attrs['employee_id'] ?? $attrs['user_id'] ?? $this->slipEmployeeId($slip);
		if (! $employeeId) {
			return;
		}

		$y = null;
		$m = null;
		foreach ([['year', 'month'], ['salary_year', 'salary_month'], ['pay_year', 'pay_month'], ['slip_year', 'slip_month']] as [$yc, $mc]) {
			if (isset($attrs[$yc], $attrs[$mc]) && $attrs[$yc] !== null && $attrs[$mc] !== '') {
				$y = (int) $attrs[$yc];
				$m = (int) $attrs[$mc];
				break;
			}
		}
		if ($y === null || $m === null) {
			if (is_object($slip) && isset($slip->year, $slip->month)) {
				$y = (int) $slip->year;
				$m = (int) $slip->month;
			}
		}
		if ($y === null || $m === null) {
			return;
		}

		$adminRow = $this->fetchSalarySlipDatabaseRow($employeeId, $y, $m);
		if ($adminRow === []) {
			return;
		}

		foreach ($adminRow as $key => $val) {
			if ($val === null || $val === '') {
				continue;
			}
			$keyStr = (string) $key;
			// Always trust admin panel values for component % / fixed amounts and types (fixes HRA / special showing 0 on app).
			if (preg_match('/_(value|type)$/', $keyStr)) {
				$attrs[$keyStr] = $val;

				continue;
			}
			if ($this->isPayslipComponentAttributeKey($keyStr)) {
				$current = $attrs[$keyStr] ?? null;
				$currentEmpty = ! array_key_exists($keyStr, $attrs) || $current === null || $current === '';
				$currentZero = is_numeric($current) && (float) $current === 0.0;
				if ($currentEmpty || ($currentZero && (float) $val !== 0.0)) {
					$attrs[$keyStr] = $val;
				}
			}
		}
	}

	protected function isPayslipComponentAttributeKey(string $key): bool
	{
		if (preg_match('/_(value|type)$/', $key)) {
			return true;
		}

		return in_array($key, [
			'basic_salary',
			'net_taxable_income',
			'total_tax_payable',
			'total_tax_recovered',
			'balance_tax_recoverable',
		], true);
	}

	/**
	 * Map alternate DB / JSON keys to admin-style names (hra, special_allowance → components).
	 */
	protected function applySalaryComponentAliases(array &$attrs): void
	{
		$map = [
			'special_allowance_value' => 'special_allow_value',
			'special_allowance_type' => 'special_allow_type',
			'house_rent_allowance_value' => 'hra_value',
			'house_rent_allowance_type' => 'hra_type',
			'hra_amount' => null,
		];

		foreach ($map as $from => $to) {
			if ($to === null) {
				continue;
			}
			if (! array_key_exists($to, $attrs) || $attrs[$to] === null || $attrs[$to] === '') {
				if (isset($attrs[$from]) && $attrs[$from] !== null && $attrs[$from] !== '') {
					$attrs[$to] = $attrs[$from];
				}
			}
		}

		$hraTypeMissing = ! array_key_exists('hra_type', $attrs) || $attrs['hra_type'] === null || $attrs['hra_type'] === '';
		if ((! isset($attrs['hra_value']) || $attrs['hra_value'] === '' || $attrs['hra_value'] === null)
			&& isset($attrs['hra']) && is_numeric($attrs['hra']) && (float) $attrs['hra'] > 0
			&& $hraTypeMissing) {
			$attrs['hra_value'] = $attrs['hra'];
			$attrs['hra_type'] = 'fixed';
		}

		$saTypeMissing = ! array_key_exists('special_allow_type', $attrs) || $attrs['special_allow_type'] === null || $attrs['special_allow_type'] === '';
		if ((! isset($attrs['special_allow_value']) || $attrs['special_allow_value'] === '' || $attrs['special_allow_value'] === null)
			&& isset($attrs['special_allowance']) && is_numeric($attrs['special_allowance']) && (float) $attrs['special_allowance'] > 0
			&& $saTypeMissing) {
			$attrs['special_allow_value'] = $attrs['special_allowance'];
			$attrs['special_allow_type'] = 'fixed';
		}
	}

	/**
	 * Merge JSON payslip payload: flat keys, or one-level nested (e.g. earnings.basic).
	 */
	protected function mergeDecodedSalaryIntoAttrs(array &$attrs, array $dec): void
	{
		foreach ($dec as $k => $v) {
			if ($k === '') {
				continue;
			}
			if (is_array($v) && $this->isAssocArray($v)) {
				foreach ($v as $sk => $sv) {
					if (! is_scalar($sv)) {
						continue;
					}
					$composite = $k.'_'.$sk;
					foreach ([$sk, $composite] as $attrKey) {
						if (! array_key_exists($attrKey, $attrs) || $attrs[$attrKey] === null || $attrs[$attrKey] === '') {
							$attrs[$attrKey] = $sv;
						}
					}
				}
			} elseif (is_scalar($v)) {
				if (! array_key_exists($k, $attrs) || $attrs[$k] === null || $attrs[$k] === '') {
					$attrs[$k] = $v;
				}
			}
		}
	}

	protected function isAssocArray(array $arr): bool
	{
		if ($arr === []) {
			return false;
		}

		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	protected function resolveNetPay($slip): float
	{
		return $this->firstNumericAttribute($slip, [
			'net_pay',
			'net_salary',
			'net_sal',
			'net',
			'take_home',
			'take_home_salary',
			'salary_net',
			'amount_payable',
			'paid_amount',
			'total_salary',
			'salary',
		]);
	}

	/**
	 * Resolve amount from DB row (case-insensitive keys, avoids empty ?? when property exists as null).
	 */
	protected function firstNumericAttribute($slip, array $keys): float
	{
		$data = $this->slipScalarData($slip);
		$lower = [];
		foreach ($data as $attrKey => $val) {
			$lower[strtolower((string) $attrKey)] = $val;
		}

		foreach ($keys as $key) {
			foreach ([$key, strtolower($key)] as $cand) {
				if (! array_key_exists($cand, $data)) {
					continue;
				}
				$v = $data[$cand];
				if ($v !== null && $v !== '' && is_numeric($v)) {
					return (float) $v;
				}
			}
			$lc = strtolower($key);
			if (array_key_exists($lc, $lower) && $lower[$lc] !== null && $lower[$lc] !== '' && is_numeric($lower[$lc])) {
				return (float) $lower[$lc];
			}
		}

		if (is_object($slip)) {
			foreach ($keys as $key) {
				if (! isset($slip->{$key})) {
					continue;
				}
				$v = $slip->{$key};
				if ($v !== null && $v !== '' && is_numeric($v)) {
					return (float) $v;
				}
			}
		}

		return 0.0;
	}

	protected function hasPayslipValueColumn($slip, string $prefix): bool
	{
		$data = $this->slipScalarData($slip);

		return array_key_exists($prefix.'_value', $data)
			&& $data[$prefix.'_value'] !== null
			&& $data[$prefix.'_value'] !== '';
	}

	protected function isPercentTypeString(string $type): bool
	{
		$t = strtolower(trim($type));
		if ($t === '' || $t === 'fixed' || $t === 'rs' || $t === 'rupees' || $t === 'inr') {
			return false;
		}

		return str_contains($t, '%')
			|| $t === 'percent'
			|| $t === 'percentage';
	}

	/**
	 * Admin schema: {prefix}_value + {prefix}_type (% = percent of $percentBase, else fixed rupees).
	 */
	protected function resolveRupeeFromValueType($slip, string $prefix, float $percentBase): float
	{
		$data = $this->slipScalarData($slip);
		$vk = $prefix.'_value';
		$tk = $prefix.'_type';

		if (! array_key_exists($vk, $data) || $data[$vk] === null || $data[$vk] === '') {
			$legacyDirect = [
				'hra' => ['hra', 'house_rent_allowance', 'hra_value'],
				'special_allow' => ['special_allow', 'special_allowance', 'sa_amount'],
				'stat_bonus' => ['stat_bonus', 'statutory_bonus', 'stat_bonus_amount'],
				'pt' => ['pt', 'professional_tax', 'p_t'],
				'perquisite' => ['perquisite', 'perquisites'],
			];
			if (isset($legacyDirect[$prefix])) {
				foreach ($legacyDirect[$prefix] as $lk) {
					if (isset($data[$lk]) && $data[$lk] !== '' && is_numeric($data[$lk])) {
						return round((float) $data[$lk], 2);
					}
				}
			}

			return 0.0;
		}

		$raw = $data[$vk];
		if ($raw === null || $raw === '' || ! is_numeric($raw)) {
			return 0.0;
		}
		$val = (float) $raw;
		$rawType = $data[$tk] ?? null;
		if (is_object($rawType) && method_exists($rawType, 'value')) {
			$rawType = $rawType->value;
		}
		$typeStr = $rawType !== null && $rawType !== '' ? (string) $rawType : 'fixed';
		if ($this->isPercentTypeString($typeStr)) {
			// Values like 4166 with type "%" are usually rupees mis-labelled; real % components are typically 0–100.
			if ($val > 100) {
				return round($val, 2);
			}

			return round($percentBase * ($val / 100.0), 2);
		}

		return round($val, 2);
	}

	protected function resolveComponent($slip, string $prefix, float $percentBase, array $legacyKeys): float
	{
		if ($this->hasPayslipValueColumn($slip, $prefix)) {
			return $this->resolveRupeeFromValueType($slip, $prefix, $percentBase);
		}

		return $this->firstNumericAttribute($slip, $legacyKeys);
	}

	protected function salarySlipBreakdown($slip): array
	{
		$this->hydrateSlipFromSalarySlipsTable($slip);

		$basic = $this->resolveSlipBasic($slip);

		$uid = $this->slipEmployeeId($slip);
		$syncFromUserSalary = $uid && class_exists(\App\Models\UserSalary::class)
			&& \App\Models\UserSalary::where('user_id', $uid)->exists();

		$hra = $this->resolveComponent($slip, 'hra', $basic, [
			'hra', 'hra_amount', 'house_rent_allowance', 'hra_allowance',
		]);

		$special = $this->resolveComponent($slip, 'special_allow', $basic, [
			'special_allow', 'special_allowance', 'special_alw', 'sa_amount',
		]);

		$statBonus = $this->resolveComponent($slip, 'stat_bonus', $basic, [
			'stat_bonus', 'statutory_bonus', 'stat_bonus_amount',
		]);

		$basicArrear = $this->firstNumericAttribute($slip, ['basic_arrear', 'arrear_basic']);
		$hraArrear = $this->firstNumericAttribute($slip, ['hra_arrear', 'arrear_hra']);
		$specialArrear = $this->firstNumericAttribute($slip, ['special_arrear', 'arrear_special']);
		$statArrear = $this->firstNumericAttribute($slip, ['stat_bonus_arrear', 'arrear_stat_bonus']);

		$basicCurrent = $this->firstNumericAttribute($slip, ['current_basic', 'basic_current', 'paid_basic']);
		if ($basicCurrent <= 0 || $syncFromUserSalary) {
			$basicCurrent = $basic;
		}

		$hraCurrent = $hra;
		$specialCurrent = $special;
		$statCurrent = $statBonus;

		$basicTotal = $this->firstNumericAttribute($slip, ['basic_total', 'total_basic']);
		if ($basicTotal <= 0 || $syncFromUserSalary) {
			$basicTotal = $basicCurrent + $basicArrear;
		}

		$hraTotal = $this->firstNumericAttribute($slip, ['hra_total', 'total_hra']);
		if ($hraTotal <= 0 || $syncFromUserSalary) {
			$hraTotal = $hraCurrent + $hraArrear;
		}

		$specialTotal = $this->firstNumericAttribute($slip, ['special_total', 'total_special_allow']);
		if ($specialTotal <= 0 || $syncFromUserSalary) {
			$specialTotal = $specialCurrent + $specialArrear;
		}

		$statTotal = $this->firstNumericAttribute($slip, ['stat_bonus_total', 'total_stat_bonus']);
		if ($statTotal <= 0 || $syncFromUserSalary) {
			$statTotal = $statCurrent + $statArrear;
		}

		// Recompute gross from resolved components so DB columns cannot disagree with BASIC/HRA rows (fixes wrong 36,000 vs 15,000).
		$computedGross = $basicTotal + $hraTotal + $specialTotal + $statTotal;
		$computedLineGross = $basic + $hra + $special + $statBonus;

		$gross = $this->firstNumericAttribute($slip, [
			'gross', 'gross_salary', 'gross_earnings', 'gross_pay', 'total_gross',
		]);
		if ($gross <= 0 || abs($gross - $computedGross) > 0.01) {
			$gross = $computedGross > 0 ? $computedGross : $computedLineGross;
		}

		$grossArrear = $this->firstNumericAttribute($slip, ['gross_arrear', 'arrear_gross']);
		$grossCurrent = $this->firstNumericAttribute($slip, ['current_gross', 'gross_current']);
		if ($grossCurrent <= 0 || abs($grossCurrent - ($gross - $grossArrear)) > 0.01) {
			$grossCurrent = max(0, $gross - $grossArrear);
		}

		// Match admin SalarySlipController::show + generateSlip: every *_type "%" uses salary basic only, not gross.
		// $resolve in admin: $type === '%' ? ($value / 100) * $basic : $value;
		$percentBase = $basic;

		$pt = $this->resolveComponent($slip, 'pt', $percentBase, [
			'pt', 'p_t', 'professional_tax', 'prof_tax',
		]);

		$deductTotal = $this->firstNumericAttribute($slip, [
			'total_deductions', 'deductions_total', 'total_deduction', 'deduction_total',
		]);
		// Prefer P.T. resolved from pt_value/pt_type when present (avoids stale total_deductions on the row).
		if ($this->hasPayslipValueColumn($slip, 'pt')) {
			$deductTotal = $pt;
		} elseif ($deductTotal <= 0) {
			$deductTotal = $pt;
		}

		$perquisite = $this->resolveComponent($slip, 'perquisite', $percentBase, [
			'perquisite', 'perquisites', 'perquisite_amount',
		]);

		$taxLines = [
			'exempt_reimburse' => $this->resolveComponent($slip, 'exempt_reimburse', $percentBase, [
				'exempt_reimburse', 'exempt_reimbursement',
			]),
			'deduction_10' => $this->resolveComponent($slip, 'deduction_10', $percentBase, [
				'deduction_10', 'deduction_u_s_10', 'deduction_us_10',
			]),
			'deduction_16' => $this->resolveComponent($slip, 'deduction_16', $percentBase, [
				'deduction_16', 'std_deduction', 'standard_deduction',
			]),
			'deduction_24' => $this->resolveComponent($slip, 'deduction_24', $percentBase, [
				'deduction_24', 'housing_loss',
			]),
			'deduction_via' => $this->resolveComponent($slip, 'deduction_via', $percentBase, [
				'deduction_via', 'deduction_chapter_via', 'chapter_via',
			]),
		];

		$earnings = [
			'basic'           => $basic,
			'basic_current'   => $basicCurrent,
			'basic_arrear'    => $basicArrear,
			'basic_total'     => $basicTotal,
			'hra'             => $hra,
			'hra_current'     => $hraCurrent,
			'hra_arrear'      => $hraArrear,
			'hra_total'       => $hraTotal,
			'special_allow'   => $special,
			'special_current' => $specialCurrent,
			'special_arrear'  => $specialArrear,
			'special_total'   => $specialTotal,
			'stat_bonus'      => $statBonus,
			'stat_current'    => $statCurrent,
			'stat_arrear'     => $statArrear,
			'stat_total'      => $statTotal,
			'gross'           => $gross,
			'gross_current'   => $grossCurrent,
			'gross_arrear'    => $grossArrear,
			'perquisite'      => $perquisite,
		];

		$monthlyGross = $this->firstNumericAttribute($slip, ['monthly_gross', 'gross_monthly']);
		if ($monthlyGross <= 0 || abs($monthlyGross - $computedLineGross) > 0.01) {
			$monthlyGross = $computedLineGross;
		}
		$earnings['monthly_gross'] = $monthlyGross;

		$deductions = [
			'pt'    => $pt,
			'total' => $deductTotal,
		];

		return compact('earnings', 'deductions', 'taxLines');
	}
}

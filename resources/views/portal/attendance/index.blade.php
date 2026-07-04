@extends('layouts.portal')

@section('title', 'Attendance')

@section('page_subtitle')
Review your monthly punch in and punch out history.
@endsection

@section('content')
@php
    $queryBase = ['month' => $month, 'year' => $year];
@endphp

<div class="attendance-app-page">
    <div class="attendance-summary-grid">
        <div class="attendance-summary-item attendance-summary-present">
            <i class="bi bi-check-circle-fill"></i>
            <div>
                <strong>{{ $stats['present'] }} days</strong>
                <span>Present</span>
            </div>
        </div>
        <div class="attendance-summary-item attendance-summary-holiday">
            <i class="bi bi-star-fill"></i>
            <div>
                <strong>{{ $stats['holiday'] }} days</strong>
                <span>Holiday</span>
            </div>
        </div>
        <div class="attendance-summary-item attendance-summary-half">
            <i class="bi bi-star"></i>
            <div>
                <strong>{{ $stats['half_day'] }} days</strong>
                <span>Half day</span>
            </div>
        </div>
        <div class="attendance-summary-item attendance-summary-absent">
            <i class="bi bi-airplane"></i>
            <div>
                <strong>{{ $stats['absent'] }} days</strong>
                <span>Absent</span>
            </div>
        </div>
        <div class="attendance-summary-item attendance-summary-late">
            <i class="bi bi-clock"></i>
            <div>
                <strong>{{ $stats['late'] }} days</strong>
                <span>Late</span>
            </div>
        </div>
        <div class="attendance-summary-item attendance-summary-early">
            <i class="bi bi-person-walking"></i>
            <div>
                <strong>{{ $stats['early_going'] }} days</strong>
                <span>Early Going</span>
            </div>
        </div>
    </div>

    <div class="app-card attendance-calendar-card">
        <form method="GET" class="attendance-month-filter">
            <input type="hidden" name="tab" value="{{ $tab }}">
            @if($selectedDay)
                <input type="hidden" name="date" value="{{ $selectedDay['date'] }}">
            @endif
            <div class="attendance-month-select-wrap">
                <i class="bi bi-chevron-down"></i>
                <select name="month" class="attendance-month-select">
                    @foreach($monthOptions as $option)
                        <option value="{{ $option['month'] }}" data-year="{{ $option['year'] }}" @selected($option['selected'])>{{ $option['label'] }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="year" id="attendanceYearInput" value="{{ $year }}">
            </div>
        </form>

        <div class="attendance-calendar-grid">
            @foreach($calendar as $day)
                @php
                    $isSelected = ($selectedDay['date'] ?? '') === $day['date'];
                    $dayQuery = array_merge($queryBase, ['date' => $day['date'], 'tab' => $tab]);
                @endphp
                <a href="{{ $day['is_future'] ? '#' : route('portal.attendance.index', $dayQuery) }}"
                   class="attendance-day-pill attendance-day-{{ $day['status_key'] }} {{ $isSelected ? 'is-selected' : '' }} {{ $day['is_future'] ? 'is-future' : '' }}"
                   @if($day['is_future']) onclick="return false;" @endif>
                    <span class="attendance-day-name">{{ $day['day_name'] }}</span>
                    <span class="attendance-day-number">{{ $day['day_number'] }}</span>
                    <span class="attendance-day-icon">
                        @switch($day['status_key'])
                            @case('holiday')
                            @case('weekend')
                                <i class="bi bi-star-fill"></i>
                                @break
                            @case('absent')
                                <i class="bi bi-airplane"></i>
                                @break
                            @case('late')
                                <i class="bi bi-clock"></i>
                                @break
                            @case('half_day')
                                <i class="bi bi-star"></i>
                                @break
                            @case('early_going')
                                <i class="bi bi-person-walking"></i>
                                @break
                            @case('present')
                                <i class="bi bi-check-lg"></i>
                                @break
                            @default
                        @endswitch
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="attendance-tab-bar">
        <a href="{{ route('portal.attendance.index', array_merge($queryBase, ['date' => $selectedDay['date'], 'tab' => 'entries'])) }}"
           class="attendance-tab {{ $tab === 'entries' ? 'active' : '' }}">In / Out Entries</a>
        <a href="{{ route('portal.attendance.index', array_merge($queryBase, ['date' => $selectedDay['date'], 'tab' => 'holiday'])) }}"
           class="attendance-tab {{ $tab === 'holiday' ? 'active' : '' }}">Holiday</a>
    </div>

    @if($tab === 'holiday')
        <div class="app-card attendance-detail-card">
            <h5 class="attendance-detail-date">Holidays in {{ $monthLabel }}</h5>
            <hr>
            @forelse($holidayList as $holiday)
                <div class="attendance-holiday-row">
                    <div>
                        <strong>{{ $holiday['name'] }}</strong>
                        <small class="d-block text-muted">{{ $holiday['date'] }}</small>
                    </div>
                    @if($holiday['is_optional'])
                        <span class="app-badge">Optional</span>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">No holidays scheduled for this month.</p>
            @endforelse
        </div>
    @else
        <div class="app-card attendance-detail-card attendance-selected-card">
            <div class="attendance-selected-head">
                <h5 class="attendance-detail-date mb-0">{{ $selectedDay['display_date'] }}</h5>
                @if(!empty($selectedDay['holiday_name']))
                    <span class="attendance-status-chip attendance-status-present">Holiday</span>
                @elseif($selectedDay['status'] !== 'Upcoming')
                    <span class="attendance-status-chip attendance-status-{{ $selectedDay['status_key'] }}">{{ $selectedDay['status'] }}</span>
                @endif
            </div>
            <div class="attendance-selected-punch">
                <div><small>Punch In</small><strong>{{ $selectedDay['punch_in'] ?? '-' }}</strong></div>
                <div><small>Punch Out</small><strong>{{ $selectedDay['punch_out'] ?? '-' }}</strong></div>
            </div>
        </div>

        <div class="app-card attendance-month-entries">
            <h6 class="attendance-month-entries-title">Full Month Punch In / Out</h6>
            <div class="portal-table-wrap">
                <table class="portal-table attendance-entry-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Punch In</th>
                            <th>Punch Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthEntries as $day)
                            @php
                                $isSelected = ($selectedDay['date'] ?? '') === $day['date'];
                                $entryQuery = array_merge($queryBase, ['date' => $day['date'], 'tab' => 'entries']);
                            @endphp
                            <tr class="{{ $isSelected ? 'is-selected-row' : '' }}"
                                onclick="window.location='{{ route('portal.attendance.index', $entryQuery) }}'">
                                <td><strong>{{ $day['display_date'] }}</strong></td>
                                <td>{{ $day['punch_in'] ?? '-' }}</td>
                                <td>{{ $day['punch_out'] ?? '-' }}</td>
                                <td>
                                    @if(!empty($day['holiday_name']))
                                        <span class="attendance-status-chip attendance-status-present">Holiday</span>
                                    @elseif($day['status'] === 'Weekend')
                                        <span class="attendance-status-chip attendance-status-present">Weekend</span>
                                    @else
                                        <span class="attendance-status-chip attendance-status-{{ $day['status_key'] }}">{{ $day['status'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const monthSelect = document.querySelector('.attendance-month-select');
    const yearInput = document.getElementById('attendanceYearInput');
    if (!monthSelect || !yearInput) return;

    monthSelect.addEventListener('change', function () {
        const option = monthSelect.options[monthSelect.selectedIndex];
        yearInput.value = option.dataset.year || yearInput.value;
        monthSelect.form.submit();
    });
})();
</script>
@endpush

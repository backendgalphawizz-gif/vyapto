{{-- resources/views/admin/vehicles/today-km-summary.blade.php --}}
@php
    $layout = \Illuminate\Support\Facades\View::exists('layouts.admin') ? 'layouts.admin' : (\Illuminate\Support\Facades\View::exists('admin') ? 'admin' : 'layouts.admin');
@endphp
@extends($layout)

@section('title', 'Today’s KM summary')

@section('content')
<div class="main-section">
    <div class="sticky-top bg-white py-2 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Today’s KM summary</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.vehicle-usage.index') }}#today-km-summary"
               class="btn btn-primary btn-rounded d-flex align-items-center gap-1">
                <i class="bi bi-list-ul"></i> All vehicle usage
            </a>
        </div>
    </div>

    <div class="bg-white border rounded p-3 mb-3">
        <form method="get" action="{{ route('admin.vehicle-usage.today-km-summary') }}" class="row g-2 align-items-end">
            <div class="col-lg-3 col-md-4">
                <label class="form-label small mb-0">Date</label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ $summaryDate->toDateString() }}" max="{{ now()->toDateString() }}">
            </div>
            <div class="col-lg-2 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm btn-rounded">Show</button>
                <a href="{{ route('admin.vehicle-usage.today-km-summary') }}" class="btn btn-outline-secondary btn-sm btn-rounded">Today</a>
            </div>
            <div class="col-lg-3 col-md-3">
                <label for="summarySearchInput" class="form-label small mb-0">Search user / vehicle</label>
                <input type="text"
                       id="summarySearchInput"
                       class="form-control form-control-sm"
                       placeholder="Type user name or vehicle number...">
            </div>
            <div class="col-lg-2 col-md-12 d-flex">
                <button type="button" id="summarySearchClear" class="btn btn-outline-secondary btn-sm btn-rounded w-100">Clear</button>
            </div>
        </form>
    </div>

    <h6 class="fw-bold mb-2">KM by driver <span class="text-muted fw-normal small">(click "Show details")</span>
        <span class="badge text-bg-light border ms-1">{{ $summaryDate->format('l, d M Y') }}</span>
    </h6>
    <div class="table-container mb-4" id="today-km-summary">
        <table class="table table-hover table-bordered mb-0 text-center">
            <thead>
                <tr>
                    <th class="text-start">Vehicle &amp; driver</th>
                    <th style="width: 16%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $rowsByUser = collect($todayKmSummary ?? [])->groupBy(function ($row) {
                        return (string) ($row->user_id ?? 'unknown');
                    })->values();
                @endphp
                @forelse($rowsByUser as $userRows)
                    @php
                        $firstRow = $userRows->first();
                        $detailId = 'km-user-detail-' . $loop->index;
                        $vehicleList = $userRows->pluck('vehicle_number')->filter()->unique()->values()->implode(', ');
                        $searchText = strtolower(trim(($firstRow->user_name ?? '') . ' ' . $vehicleList));
                    @endphp
                    <tr class="summary-row" data-search="{{ $searchText }}">
                        <td class="text-start">
                            <strong>{{ $firstRow->user_name ?? 'User #' . ($firstRow->user_id ?? '—') }}</strong><br>
                            <small class="text-muted">Vehicles: {{ $vehicleList ?: '—' }}</small>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-rounded"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#{{ $detailId }}"
                                    aria-expanded="false"
                                    aria-controls="{{ $detailId }}">
                                Show details
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse summary-detail-row" id="{{ $detailId }}">
                        <td colspan="2" class="text-start bg-light">
                            <div class="table-responsive py-2">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Vehicle</th>
                                            <th>1st entry (start KM)</th>
                                            <th>2nd entry (KM)</th>
                                            <th>Difference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($userRows as $entry)
                                            <tr>
                                                <td>{{ $entry->vehicle_number ?? '—' }}</td>
                                                <td>
                                                    @if($entry->start_km !== null)
                                                        <strong>{{ number_format($entry->start_km, 2) }}</strong> km
                                                        <br><small class="text-muted"><i class="bi bi-clock"></i> {{ $entry->first_entry_at?->format('H:i:s') ?? '—' }}</small>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($entry->end_km !== null)
                                                        <strong>{{ number_format($entry->end_km, 2) }}</strong> km
                                                        <br><small class="text-muted"><i class="bi bi-clock"></i> {{ $entry->second_entry_at?->format('H:i:s') ?? '—' }}</small>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($entry->difference_km !== null)
                                                        <strong class="text-primary">{{ number_format($entry->difference_km, 2) }}</strong> km
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-muted py-4">No vehicle usage for this date.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('summarySearchInput');
    var clearBtn = document.getElementById('summarySearchClear');
    if (!input || !clearBtn) return;

    function filterRows() {
        var term = input.value.toLowerCase().trim();
        var mainRows = document.querySelectorAll('#today-km-summary .summary-row');

        mainRows.forEach(function (row) {
            var searchable = (row.dataset.search || '').toLowerCase();
            var show = term === '' || searchable.indexOf(term) !== -1;
            row.style.display = show ? '' : 'none';

            var detailRow = row.nextElementSibling;
            if (detailRow && detailRow.classList.contains('summary-detail-row') && !show) {
                detailRow.classList.remove('show');
                detailRow.style.display = 'none';
            } else if (detailRow && detailRow.classList.contains('summary-detail-row') && show) {
                detailRow.style.display = '';
            }
        });
    }

    input.addEventListener('input', filterRows);
    clearBtn.addEventListener('click', function () {
        input.value = '';
        filterRows();
        input.focus();
    });
});
</script>
@endpush

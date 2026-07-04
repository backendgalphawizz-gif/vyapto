@extends('layouts.portal')

@section('title', 'Salary')

@section('content')
<div class="app-card mb-4">
    <form method="GET" class="filter-bar" style="max-width: 420px;">
        <select name="year" class="form-select form-select-dark">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
            @endfor
        </select>
        <button class="app-btn app-btn-green app-btn-sm">Filter Year</button>
    </form>
</div>

@if($records->isEmpty())
    <div class="app-card text-center text-muted">No salary records for {{ $year }}.</div>
@else
    <div class="app-card d-none d-md-block">
        <div class="portal-table-wrap">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Net Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>{{ optional($record->date)->format('F Y') }}</td>
                            <td>₹{{ number_format((float) $record->net_salary, 2) }}</td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('portal.salary.show', optional($record->date)->format('Y-m-01')) }}" class="app-link">View</a>
                                <a href="{{ $record->pdf_url }}" target="_blank" class="app-link">PDF</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="content-grid-2 d-md-none">
        @foreach($records as $record)
            <div class="app-card">
                <div class="app-card-header">
                    <div>
                        <strong>{{ optional($record->date)->format('F Y') }}</strong>
                        <small class="d-block text-muted">Net ₹{{ number_format((float) $record->net_salary, 2) }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('portal.salary.show', optional($record->date)->format('Y-m-01')) }}" class="app-link">View</a>
                        <a href="{{ $record->pdf_url }}" target="_blank" class="app-link">PDF</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

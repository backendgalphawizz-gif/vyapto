{{--
    Path: resources/views/admin/partials/export-dropdown.blade.php
    View:  admin.partials.export-dropdown

    If your error says admin.partials.export_dropdown (underscore), add the duplicate file:
    resources/views/admin/partials/export_dropdown.blade.php
    Or change @include to admin.partials.export-dropdown (hyphen) to match this file.
--}}
@php
    $exportQuery = $exportQuery ?? [];
@endphp
<div class="btn-group me-2">
    <button type="button" class="btn btn-outline-secondary dropdown-toggle rounded-3" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-download me-1"></i> Export
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li>
            <a class="dropdown-item" href="{{ route($exportRoute, array_merge($exportQuery, ['format' => 'pdf'])) }}">
                <i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route($exportRoute, array_merge($exportQuery, ['format' => 'xlsx'])) }}">
                <i class="bi bi-file-earmark-excel text-success me-2"></i> Excel
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route($exportRoute, array_merge($exportQuery, ['format' => 'csv'])) }}">
                <i class="bi bi-filetype-csv text-primary me-2"></i> CSV
            </a>
        </li>
    </ul>
</div>

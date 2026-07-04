@props(['name', 'label'])

<th {{ $attributes->merge(['class' => 'text-start']) }}>
    <a href="{{ route(request()->route()->getName(), array_merge(request()->query(), ['sort_by' => $name, 'sort_order' => request('sort_by') == $name && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
        {{ $label }}
        @if(request('sort_by') == $name)
            <i class="bi bi-arrow-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
        @else
            <i class="bi bi-arrow-down-up text-muted small"></i>
        @endif
    </a>
</th>
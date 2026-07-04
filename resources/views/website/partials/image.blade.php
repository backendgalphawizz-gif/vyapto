@if($imageUrl ?? null)
    <img src="{{ $imageUrl }}" alt="{{ $alt ?? '' }}" class="{{ $class ?? 'section-image' }}">
@endif

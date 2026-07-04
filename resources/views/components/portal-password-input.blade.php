@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'inputClass' => 'app-input',
])

@php
    $inputId = $id ?? $name;
@endphp

<div {{ $attributes->merge(['class' => 'app-input-wrap']) }}>
    @if($label)
        <label for="{{ $inputId }}">{!! $label !!}</label>
    @endif
    <div class="password-toggle-wrap">
        <input
            type="password"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class="{{ $inputClass }}"
            @if($required) required @endif
        >
        <button type="button" class="password-toggle-btn" aria-label="Show password" data-password-toggle>
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>

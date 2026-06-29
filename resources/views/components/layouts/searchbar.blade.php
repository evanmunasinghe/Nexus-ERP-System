@props([
    'action',
    'placeholder' => 'Search records...',
    'value' => null,
])

@php
    $searchValue = $value ?? request('search');
@endphp

<form action="{{ $action }}" method="GET" class="mb-4 no-print">
    <div class="input-group shadow-sm">
        <span class="input-group-text bg-white">
            <i class="fa-solid fa-magnifying-glass text-secondary"></i>
        </span>
        <input
            type="search"
            name="search"
            value="{{ $searchValue }}"
            class="form-control"
            placeholder="{{ $placeholder }}"
            aria-label="{{ $placeholder }}"
        >
        @if(filled($searchValue))
            <a href="{{ $action }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-xmark me-1"></i> Clear
            </a>
        @endif
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Search
        </button>
    </div>
</form>

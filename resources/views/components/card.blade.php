@php $passed = isset($data['passed']) && $data['passed'] @endphp
<div class="card card-with-shadow-static">
    <div class="card-header text-center {{ isset($override_header_color) ? $override_header_color : ($passed ? 'camppass-orange' : 'bg-secondary text-white') }}">{{ $header }}</div>
    <div class="card-body">
        @if (isset($data['text']))
            <p class="card-text{{ !$passed ? ' text-muted' : '' }}">{{ $data['text'] }}</p>
        @endif
        @if (isset($extra_body))
            @if (!$passed)
                <div class="text-muted">
            @endif
                {{ $extra_body }}
            @if (!$passed)
                </div>
            @endif
        @endif
        @if (isset($buttons) && isset($data['button']) && $data['button'])
            <div class="d-flex">
                {{ $buttons }}
            </div>
        @endif
    </div>
</div>
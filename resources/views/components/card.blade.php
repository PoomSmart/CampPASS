<div class="card card-with-shadow-static">
    <div class="card-header text-center {{ isset($override_header_color) ? $override_header_color : ($data['passed'] ? 'camppass-orange' : 'bg-secondary text-white') }}">{{ $header }}</div>
    <div class="card-body">
        @if (isset($data['text']))
            <p class="card-text">{{ $data['text'] }}</p>
        @endif
        @if (isset($extra_body))
            {{ $extra_body }}
        @endif
        @if (isset($buttons) && isset($data['button']) && $data['button'])
            <div class="d-flex">
                {{ $buttons }}
            </div>
        @endif
    </div>
</div>
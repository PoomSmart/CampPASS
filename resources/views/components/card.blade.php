<div class="card card-with-shadow-static">
    <div class="card-header text-center camppass-orange">{{ $header }}</div>
    <div class="card-body">
        @if (isset($data['text']))
            <p class="card-text">{{ $data['text'] }}</p>
        @endif
        @if (isset($buttons) && isset($data['button']) && $data['button'])
            <div class="d-flex">
                {{ $buttons }}
            </div>
        @endif
    </div>
</div>
<div class="card card-with-shadow">
    <a href="{{ route('camps.show', $object->id) }}">
        <img class="card-img-top card-img-fixed" src="{{ $object->getBannerPath($actual = false, $display = true) }}" alt="Image of {{ $object }}">
        <div class="card-body">
            <h5 class="card-title text-truncate" title="{{ $object }}">{{ $object }}</h5>
            @foreach ($object->getTags() as $glyph => $tag)
                <label class="badge badge-secondary font-weight-normal"><i class="{{ $glyph }} fa-xs mr-1"></i>{{ $tag }}</label>
            @endforeach
            <p class="text-muted text-truncate mb-0" title="{{ $object->organization }}">@lang('app.By') {{ $object->organization }}</p>
            <div class="my-2 list-group">
                <span class="text-muted"><i class="fa fa-calendar mr-2"></i>{{ $object->getEventStartDate() }}</span>
                <span class="text-muted"><i class="fas fa-globe-asia mr-2"></i>Location X</span>
            </div>
            @if (!auth()->user() || auth()->user()->isCamper())
                @php
                    $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($object, $short = true, $auth_check = true);
                    $apply_text = $info['text'];
                    $disabled = $info['disabled'];
                    $route = $info['route'];
                @endphp
                <a class="btn btn-primary text-truncate mt-2 w-100{{ $disabled ? ' disabled' : ''}}" href="{{ $route }}#apply"><i class="far fa-file-alt mr-2 fa-xs"></i>{{ $apply_text }}</a>
            @endif
            @php
                $close_date = $object->getAppCloseDateHuman();
            @endphp
            @if ($close_date)
                <p class="card-text text-center mt-2"><small class="text-muted">{{ $close_date }}</small></p>
            @endif
        </div>
    </a>
</div>
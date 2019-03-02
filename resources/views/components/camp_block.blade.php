<div class="card">
    <a href="{{ route('camps.show', $object->id) }}">
        <img class="card-img-top" src="{{ isset($src) ? $src : asset('/images/placeholders/Camp '.\App\Common::randomInt10().'.png') }}" alt="Image of {{ $object }}">
        <div class="card-body">
            <h5 class="card-title text-truncate" title="{{ $object }}">{{ $object }}</h5>
            <h6 class="text-muted">({{ $object->camp_procedure }})</h6>
            <p class="text-muted text-truncate mb-0" title="{{ $object->organization }}">@lang('app.By') {{ $object->organization }}</p>
            <div class="my-2 list-group">
                <span class="text-muted"><i class="fa fa-calendar mr-2"></i>{{ $object->getEventStartDate() }}</span>
                <span class="text-muted"><i class="fas fa-globe-asia mr-2"></i>Location X</span>
            </div>
            @if (!\Auth::user() || \Auth::user()->isCamper())
                @php
                    $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($object, $short = true);
                    $apply_text = $info['text'];
                    $disabled = $info['disabled'];
                    $route = $info['route'];
                @endphp
                <a class="btn btn-primary mt-2 w-100{{ $disabled ? ' disabled' : ''}}" href="{{ $route }}">{{ $apply_text }}</a>
            @endif
            @php
                $close_date = $object->getCloseDate();
            @endphp
            @if ($close_date)
                <p class="card-text text-center mt-2"><small class="text-muted">@lang('registration.WillClose') {{ $close_date }}</small></p>
            @endif
        </div>
    </a>
</div>
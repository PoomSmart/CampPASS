<div class="card">
    <img class="card-img-top" src="{{ isset($src) ? $src : 'http://placehold.it/800x600/'.\App\Common::randomString(6) }}" alt="Image of {{ $object }}">
    <div class="card-body">
        <h5 class="card-title"><a href="{{ route('camps.show', $object) }}">{{ $object }}</a></h5>
        <p class="text-muted mb-0">@lang('app.By') {{ $object->organization() }}</p>
        <div class="my-2 list-group">
            <span class="text-muted"><i class="fa fa-calendar mr-2"></i>{{ $object->getEventStartDate() }}</span>
            <span class="text-muted"><i class="fas fa-globe-asia mr-2"></i>Location X</span>
        </div>
        @php
            $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($object, $short = true);
            $apply_text = $info['text'];
            $disabled = $info['disabled'];
            $route = isset($info['route']) ? $info['route'] : 'camp_application.landing';
        @endphp
        <a class="btn btn-primary mt-2 w-100{{ $disabled ? ' disabled' : ''}}" href="{{ route($route, $object->id) }}">{{ $apply_text }}</a>
        @if ($object->getCloseDate())
            <p class="card-text text-center mt-2"><small class="text-muted">@lang('registration.WillClose') {{ $object->getCloseDate() }}</small></p>
        @endif
    </div>
</div>
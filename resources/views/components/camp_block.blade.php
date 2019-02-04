<div class="card">
    <img class="card-img-top" src="{{ isset($src) ? $src : 'http://placehold.it/800x600/'.\App\Common::randomString(6) }}" alt="Image of {{ $object }}">
    <div class="card-body">
        <h5 class="card-title"><a href="{{ route('camps.show', $object) }}">{{ $object }}</a></h5>
        <h6 class="text-muted">{{ trans('app.By') }} {{ $object->organization() }}</h6>
        <p class="card-text">{{ $object->getShortDescription() }}</p>
        <?php
            $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($object, $short = true);
            $apply_text = $info['text'];
            $disabled = $info['disabled'];
        ?>
        <a class="btn btn-primary w-100{{ $disabled ? ' disabled' : ''}}" href="{{ route('camp_application.landing', $object->id) }}">{{ $apply_text }}</a>
        @if ($object->getCloseDate())
            <p class="card-text text-center mt-2"><small class="text-muted">{{ trans('registration.WillClose').' '.$object->getCloseDate() }}</small></p>
        @endif
    </div>
</div>
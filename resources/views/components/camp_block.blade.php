<div class="card">
    <img class="card-img-top" src="{{ $src }}" alt="Image of {{ $camp }}">
    <div class="card-body">
        <h5 class="card-title">{{ $camp }}</h5>
        <h6 class="text-muted">{{ trans('app.By') }} {{ $camp->organization() }}</h6>
        <p class="card-text">{{ $camp->getShortDescription() }}</p>
        <?php
            $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($camp);
            $apply_text = $info['text'];
            $disabled = $info['disabled'];
        ?>
        <a class="btn btn-primary w-100{{ $disabled ? ' disabled' : ''}}"
            href="{{ route('camp_application.landing', $camp->id) }}"
        >{{ $apply_text }}</a>
        @if ($camp->getCloseDate())
            <p class="card-text text-center mt-2"><small class="text-muted">{{ trans('registration.WillClose').' '.$camp->getCloseDate() }}</small></p>
        @endif
    </div>
</div>
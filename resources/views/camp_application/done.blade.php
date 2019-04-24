@extends('layouts.card')

@section('card_content')
    <div class="row text-center">
        <div class="col-12">
            <h2>@lang('qualification.ThankCampApply')</h2>
        </div>
        <div class="col-12">
            <p class="text-muted">{{ $camp }}</p>
        </div>
        <div class="col-12">
            @component('components.a', [
                'class' => 'btn btn-primary',
                'href' => route('camp_application.status', $registration->id),
                'glyph' => 'far fa-file-alt fa-xs',
                'label' => trans('registration.Status'),
            ])
            @endcomponent
        </div>
        <div class="col-12 mt-2">
            <p class="text-muted">@lang('app.RedirectTo', [
                'where' => trans('registration.Status'),
                'seconds' => 5,
            ])</p>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.location = "{!! route('camp_application.status', $registration->id) !!}"
        }, 5000);
    </script>
@endsection
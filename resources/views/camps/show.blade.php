@extends('layouts.card')

@section('header')
    {{ $camp }}
@endsection

@section('card_content')
    <p>{{ $category->getName() }} - {{ $camp->camp_procedure()->getTitle() }} - {{ $camp->getShortDescription() }}</p>
    <div class="row">
        <div class="col-12">
            <?php
                $info = \App\Http\Controllers\CampApplicationController::get_apply_button_information($camp);
                $apply_text = $info['text'];
                $disabled = $info['disabled'];
            ?>
            <a class="btn btn-primary{{ $disabled ? ' disabled' : ''}}"
                href="{{ route('camp_application.landing', $camp->id) }}"
            >{{ $apply_text }}</a>
            <a class="btn btn-secondary" target="_blank" href="{{ $camp->getURL() }}">@lang('camp.ContactCampMaker')</a>
        </div>
    </div>
@endsection
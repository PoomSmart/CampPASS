@extends('layouts.blank')

@section('header')
    @lang('camper.MyCamps')
@endsection

@section('content')
    @foreach ($categorized_registrations as $status => $registrations)
        <div class="container mt-4">
            <h3 class="mb-4">{{ $status }}</h3>
            @component('components.card_columns', [
                'objects' => $registrations,
                'getter' => 'camp',
                'component' => 'components.camp_block',
            ])
            @endcomponent
        </div>
    @endforeach
@endsection
@extends('layouts.table')

@section('header')
    @lang('camper.MyCamps')
@endsection

@section('content')
    @component('components.card_columns', [
        'objects' => $camps,
        'component' => 'components.camp_block',
    ])
    @endcomponent
@endsection
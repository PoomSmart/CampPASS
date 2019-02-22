@extends('layouts.blank')

@section('header')
    {{ $record->getName() }}
@endsection

@section('content')
    @component('components.card_columns', [
        'objects' => $camps,
        'component' => 'components.camp_block',
    ])
    @endcomponent
@endsection
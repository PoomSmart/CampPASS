@extends('layouts.blank')

@section('header')
    @lang('camper.MyCamps')
@endsection

@section('custom-width')
    <div class="col-12 col-xl-10">
@endsection

@section('content')
    @php $i = 0 @endphp
    @foreach ($categorized_registrations as $status => $registrations)
        <div class="container-fluid mt-4">
            <h3 class="mb-4" id="{{ $i++ }}">{{ $status }}</h3>
            @component('components.card_columns', [
                'objects' => $registrations,
                'getter' => 'camp',
                'component' => 'components.camp_block',
            ])
            @endcomponent
        </div>
    @endforeach
@endsection

@section('sidebar-items')
    @php $i = 0 @endphp
    @foreach ($categorized_registrations as $status => $registrations)
        <li class="nav-item"><a class="nav-link rounded pl-2{{ $i == 0 ? ' active' : '' }}" data-toggle="scroll" href="#{{ $i++ }}"><b>{{ $status }}</b></a></li>
    @endforeach
@endsection
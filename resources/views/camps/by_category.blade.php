@extends('layouts.blank')

@section('header')
    {{ $record->getName() }}
    @if (isset($year))
        - {{ $year }}
    @elseif (isset($organization))
        - {{ $organization }}
    @endif
@endsection

@section('custom-width')
    <div class="col-12 col-xl-10">
@endsection

@section('sidebar-items')
    @php $i = 0 @endphp
    @foreach ($categorized_camps as $category => $camps)
        <li class="nav-item"><a class="nav-link rounded{{ $i == 0 ? ' active' : '' }}" data-toggle="scroll" href="#{{ $i++ }}"><b>{{ $category }}</b></a></li>
    @endforeach
@endsection

@section('content')
    @php $i = 0 @endphp
    @foreach ($categorized_camps as $category => $camps)
        <div class="container-fluid mt-4">
            <h3 class="mb-4 d-inline-block" id="{{ $i++ }}">{{ $category }}</h3>
            @component('components.card_columns', [
                'objects' => $camps,
                'component' => 'components.camp_block',
            ])
            @endcomponent
        </div>
    @endforeach
@endsection
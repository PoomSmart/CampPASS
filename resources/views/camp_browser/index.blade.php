@extends('layouts.table')

@section('header')
    Camp Browser
@endsection

@section('content')
    <div class="container mt-2">
        @foreach ($camps as $index => $camp)
            @if ($index % 3 == 0)
                <div class="align-items-start card-columns no-gutters">
            @endif
            @component('components.camp_block', [
                'src' => 'http://placehold.it/800x600/'.\App\Common::randomString(6),
                'camp' => $camp,
            ])
            @endcomponent
            @if (($index + 1) % 3 == 0)
                </div>
            @endif
        @endforeach
    </div>
@endsection
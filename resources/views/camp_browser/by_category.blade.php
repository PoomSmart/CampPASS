@extends('layouts.table')

@section('header')
    {{ $record->getName() }}
@endsection

@section('content')
    @foreach ($camps as $index => $camp)
        @if ($index % 3 == 0)
            <div class="card-columns">
        @endif
        @component('components.camp_block', [
            'object' => $camp,
        ])
        @endcomponent
        @if (($index + 1) % 3 == 0)
            </div>
        @endif
    @endforeach
    @if (count($camps) % 3)
        </div>
    @endif
@endsection
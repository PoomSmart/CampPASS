@extends('layouts.table')

@section('header')
    Camp Browser
@endsection

@section('content')
    @foreach ($categorized_camps as $category => $camps)
        <div class="container mt-4">
            <h3 class="mb-4">{{ $category }}</h3>
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
            @if (count($camps) % 3)
                </div>
            @endif
        </div>
    @endforeach
@endsection
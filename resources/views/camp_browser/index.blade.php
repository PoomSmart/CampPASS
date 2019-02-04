@extends('layouts.table')

@section('header')
    View All Camps
@endsection

@section('content')
    @foreach ($categorized_camps as $category => $camps)
        <div class="container mt-4">
            <h3 class="mb-4"><a href="{{ route('camp_browser.by_category', $category_ids[$category]) }}">{{ $category }}</a></h3>
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
        </div>
    @endforeach
@endsection
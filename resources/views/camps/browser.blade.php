@extends('layouts.blank')

@section('header')
    @lang('camp.ViewAllCamps')
@endsection

@section('content')
    @foreach ($categorized_camps as $category => $camps)
        <div class="container mt-4">
            <h3 class="mb-4 d-inline-block">{{ $category }}</h3>
            <a class="ml-3 d-inline-block" href="{{ route('camps.by_category', $category_ids[$category]) }}">@lang('app.More')</a>
            @component('components.card_columns', [
                'objects' => $camps,
                'component' => 'components.camp_block',
            ])
            @endcomponent
        </div>
    @endforeach
@endsection
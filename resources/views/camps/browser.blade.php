@extends('layouts.blank')

@section('header')
    @lang('camp.BrowseCamps')
@endsection

@section('custom-width')
    <div class="col-sm-12 col-lg-10 col-xl-7">
@endsection

@section('content')
    <form class="row form" action="{{ route('camps.browser') }}" method="GET">
        <div class="col-md-8">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <span>@lang('app.SearchCampByName')</span>
                    </div>
                </div>
                @component('components.input', [
                    'name' => 'query',
                    'value' => Request::get('query'),
                ])
                @endcomponent
            </div>
            @component('components.input', [
                'name' => 'test',
                'input_type' => 'checkbox',
                'idx' => 1,
                'objects' => [ '1', '2', '3' ],
            ])
            @endcomponent
        </div>
        <div class="col-md-4">
            @component('components.submit', [
                'label' => trans('app.Search'),
                'class' => 'mr-2',
            ])
            @endcomponent
            <a href="{{ route('camps.browser') }}" class="btn btn-outline-info">@lang('app.ClearFilters')</a>
        </div>
    </form>
    @foreach ($categorized_camps as $category => $camps)
        <div class="container-fluid mt-4">
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
@extends('layouts.blank')

@section('header')
    @lang('camp.BrowseCamps')
@endsection

@section('content')
    <form class="form-inline" action="{{ route('camps.browser') }}" method="GET">
        @component('components.input', [
            'name' => 'query',
            'value' => Request::get('query'),
            'label' => trans('app.SearchCampByName'),
            'label_class' => 'mr-2',
            'class' => 'mr-2 w-50',
        ])
        @endcomponent
        <!--<select name="category" id="">
            <option value="auto">Auto</option>
            <option value="moto">Moto</option>
        </select>-->
        @component('components.submit', [
            'label' => trans('app.Search'),
            'class' => 'mr-2',
        ])
        @endcomponent
        <a href="{{ route('camps.browser') }}" class="btn btn-outline-info">@lang('app.ClearFilters')</a>
    </form>
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
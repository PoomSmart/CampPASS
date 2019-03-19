@extends('layouts.blank')

@section('header')
    @lang('camp.BrowseCamps')
@endsection

@section('custom-width')
    <div class="col-12 col-xl-10">
@endsection

@section('content')
    <form class="row form" action="{{ route('camps.browser') }}" method="GET">
        <div class="col-lg-8 px-0">
            <div class="input-group">
                @component('components.input', [
                    'name' => 'query',
                    'value' => Request::get('query'),
                    'placeholder' => trans('app.SearchCampByName'),
                ])
                @endcomponent
            </div>
        </div>
        <div class="col-lg-4">
            @component('components.submit', [
                'label' => trans('app.Search'),
                'class' => 'btn btn-primary mr-2',
                'glyph' => 'fas fa-search fa-xs ',
            ])
            @endcomponent
            <a href="{{ route('camps.browser') }}" class="btn btn-secondary"><i class="fas fa-filter fa-xs mr-1"></i>@lang('app.ClearFilters')</a>
        </div>
        <div class="col-12 px-0">
            <div class="form-group d-inline-flex mt-2">
                <div class="d-inline-block mr-4">
                    @component('components.label', [
                        'name' => 'year',
                        'label' => trans('camp.CampFor'),
                        'label_class' => 'py-0 font-weight-bold',
                    ])
                    @endcomponent
                </div>
                <div class="d-inline-block my-auto">
                    @component('components.input', [
                        'name' => 'year',
                        'input_type' => 'radio',
                        'objects' => $years,
                        'value' => $year,
                        'nowrapper' => 1,
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
    </form>
    @php $i = 0 @endphp
    @foreach ($categorized_camps as $category => $camps)
        <div class="container-fluid mt-4">
            <h3 class="mb-4 d-inline-block" id="{{ $i++ }}">{{ $category }}</h3>
            <a target="_blank" class="ml-3 d-inline-block" href="{{ route('camps.by_category_year', ['record' => $category_ids[$category], 'year' => $year]) }}">@lang('app.More')</a>
            @component('components.card_columns', [
                'objects' => $camps,
                'component' => 'components.camp_block',
            ])
            @endcomponent
        </div>
    @endforeach
@endsection

@section('sidebar-items')
    @php $i = 0 @endphp
    @foreach ($categorized_camps as $category => $camps)
        <li class="nav-item"><a class="nav-link rounded{{ $i == 0 ? ' active' : '' }}" data-toggle="scroll" href="#{{ $i++ }}"><b>{{ $category }}</b></a></li>
    @endforeach
@endsection
@extends('layouts.blank')

@section('header')
    @lang('camp.BrowseCamps')
@endsection

@section('custom-width')
    <div class="col-12 col-xl-10">
@endsection

@section('content')
    <form class="row form" action="{{ route('camps.browser') }}" method="GET">
        <div class="col-sm-12 col-lg-8 px-0">
            <div class="input-group">
                @component('components.input', [
                    'name' => 'query',
                    'value' => Request::get('query'),
                    'placeholder' => trans('app.SearchCampByName'),
                ])
                @endcomponent
            </div>
        </div>
        <div class="col-sm-12 col-lg-4 d-inline-flex mt-2 mt-md-2 mt-lg-0 px-0 px-lg-2 px-md-0">
            @component('components.submit', [
                'label' => trans('app.Search'),
                'class' => 'btn btn-primary mr-2 w-50',
                'glyph' => 'fas fa-search fa-xs ',
            ])
            @endcomponent
            <a href="{{ route('camps.browser') }}" class="btn btn-secondary w-50"><i class="fas fa-filter fa-xs mr-1"></i>@lang('app.ClearFilters')</a>
        </div>
        <div class="col-12 px-0">
            <div class="form-group d-inline-flex mt-2 mb-0">
                <div class="d-none d-lg-inline-block mr-4">
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
        <div class="col-12 px-0">
            <div class="form-group d-inline-flex mt-2 mb-0">
                <div class="d-none d-lg-inline-block mr-4">
                    @component('components.label', [
                        'name' => 'region',
                        'label' => trans('camp.AcceptableRegions'),
                        'label_class' => 'py-0 font-weight-bold',
                    ])
                    @endcomponent
                </div>
                <div class="d-inline-block my-auto">
                    @component('components.input', [
                        'name' => 'region',
                        'input_type' => 'radio',
                        'objects' => $regions,
                        'value' => $region,
                        'nowrapper' => 1,
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
        <div class="col-12 px-0">
            <div class="form-group d-inline-flex mt-2 mb-0">
                <div class="d-none d-lg-inline-block mr-4">
                    @component('components.label', [
                        'name' => 'organization_id',
                        'label' => trans('organization.Organization'),
                        'label_class' => 'py-0 font-weight-bold',
                    ])
                    @endcomponent
                </div>
                <div class="d-inline-block my-auto">
                    @component('components.input', [
                        'name' => 'organization_id',
                        'input_type' => 'select',
                        'objects' => $organizations,
                        'value' => $organization_id,
                        'placeholder' => trans('camp.SelectOrganization'),
                        'class' => 'form-control-sm',
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
            @php
                $append = '?';
                if ($year)
                    $append = "{$append}&{$year}";
                if ($region)
                    $append = "{$append}&{$region}";
                if ($organization_id)
                    $append = "{$append}&{$organization_id}";
                if ($append == '?')
                    $append = null;
            @endphp
            <a target="_blank" class="ml-3 d-inline-block" href="{{ route('camps.by_category', [
                'record' => $category_ids[$category],
            ]) }}{{ $append }}">@lang('app.More')</a>
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
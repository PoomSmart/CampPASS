@extends('layouts.blank')

@section('header')
    @lang('camp.BrowseCamps')
@endsection

@section('custom-width')
    <div class="col-12 col-xl-10">
@endsection

@section('content')
    <form class="row form" action="{{ route('camps.browser') }}" method="GET">
        <div class="col-12 px-0">
            <div class="input-group">
                @component('components.input', [
                    'name' => 'query',
                    'value' => Request::get('query'),
                    'placeholder' => trans('app.SearchCampByName'),
                    'type' => 'search',
                ])
                @endcomponent
                <div class="input-group-append">
                    @component('components.submit', [
                        'label' => trans('app.Search'),
                        'class' => 'btn btn-primary',
                        'glyph' => 'fas fa-search fa-xs mr-0',
                    ])
                    @endcomponent
                    <button type="button"
                        class="btn btn-secondary"
                        onclick="jQuery('#filters select').val('---')"
                    ><i class="fas fa-filter fa-xs mr-1"></i>@lang('app.ClearFilters')</button>
                </div>
            </div>
        </div>
        <div class="col-12 px-0 mt-2" id="filters">
            <div class="form-row">
                <div class="col-12 col-md-4">
                    @component('components.input', [
                        'name' => 'education_level',
                        'label' => trans('camp.CampFor'),
                        'input_type' => 'select',
                        'objects' => $education_levels,
                        'value' => $education_level,
                        'getter' => 'name',
                        'placeholder' => '---',
                        'class' => 'form-control-sm',
                    ])
                    @endcomponent
                </div>
                <div class="col-12 col-md-4">
                    @component('components.input', [
                        'name' => 'region',
                        'label' => trans('camp.AcceptableRegions'),
                        'input_type' => 'select',
                        'objects' => $regions,
                        'value' => $region,
                        'placeholder' => '---',
                        'class' => 'form-control-sm',
                    ])
                    @endcomponent
                </div>
                <div class="col-12 col-md-4">
                    @component('components.input', [
                        'name' => 'organization_id',
                        'label' => trans('organization.Organization'),
                        'input_type' => 'select',
                        'objects' => $organizations,
                        'value' => $organization_id,
                        'placeholder' => '---',
                        'class' => 'form-control-sm',
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
    </form>
    @if (empty($categorized_camps))
        <div class="text-center">
            @lang('app.NoResultsFound')
        </div>
    @else
        @php $i = 0 @endphp
        @foreach ($categorized_camps as $category => $camps)
            <div class="container-fluid mt-4">
                <h3 class="mb-4 d-inline-block" id="{{ $i++ }}">{{ $category }}</h3>
                @php
                    $append = '';
                    if ($education_level)
                        $append = "{$append}&education_level{$education_level}";
                    if ($region)
                        $append = "{$append}&region={$region}";
                    if ($organization_id)
                        $append = "{$append}&organization_id={$organization_id}";
                    if ($append == '')
                        $append = null;
                    else
                        $append = '?'.substr($append, 1);
                @endphp
                <a class="ml-3 d-inline-block" href="{{ route('camps.by_category', [
                    'record' => $category_ids[$category],
                ]) }}{{ $append }}">@lang('app.More')</a>
                @component('components.card_columns', [
                    'objects' => $camps,
                    'component' => 'components.camp_block',
                ])
                @endcomponent
            </div>
        @endforeach
    @endif
@endsection

@section('sidebar-items')
    <li class="nav-item" inactive><div class="nav-link font-weight-bold disabled">@lang('camp.CampsByCategory')</div></li>
    @php $i = 0 @endphp
    @foreach ($categorized_camps as $category => $camps)
        <li class="nav-item"><a class="nav-link rounded{{ $i == 0 ? ' active' : '' }}" data-toggle="scroll" href="#{{ $i++ }}">{{ $category }}</a></li>
    @endforeach
@endsection
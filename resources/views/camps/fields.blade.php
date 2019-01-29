@section('camp-fields')

@component('components.input', [
    'name' => 'name_en',
    'label' => trans('camp.EnglishName'),
])@endcomponent

@component('components.input', [
    'name' => 'name_th',
    'label' => trans('camp.ThaiName'),
])@endcomponent

@component('components.input', [
    'name' => 'short_description_en',
    'label' => trans('camp.EnglishShortDescription'),
    'textarea' => 1,
])@endcomponent

@component('components.input', [
    'name' => 'short_description_th',
    'label' => trans('camp.ThaiShortDescription'),
    'textarea' => 1,
])@endcomponent

@component('components.input', [
    'name' => 'camp_category_id',
    'label' => trans('camp.Category'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', [
        'name' => 'camp_category_id',
        'objects' => $categories,
        'required' => 1
    ])@endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'camp_procedure_id',
    'label' => trans('camp.CampProcedure'),
    'attributes' => 'required',
])
@slot('override')
    @component('components.select', [
        'name' => 'camp_procedure_id',
        'objects' => $camp_procedures,
        'disabled' => !\Auth::user()->isAdmin() && isset($update),
    ])
    @endcomponent
@endslot
@endcomponent

<?php $can_list_organization = \Auth::user()->hasPermissionTo('organization-list'); ?>

@component('components.input', [
    'name' => 'organization_id',
    'label' => trans('camp.Organization'),
    'attributes' => $can_list_organization ? 'required' : isset($update) ? 'disabled' : '',
])
@slot('override')
    @component('components.select', [
        'name' => 'organization_id',
        'objects' => $organizations,
        'disabled' => !$can_list_organization,
    ])
    @endcomponent
@endslot
@endcomponent

@component('components.input', [
    'name' => 'acceptable_regions',
    'label' => trans('camp.AcceptableRegions'),
    'attributes' => 'required',
])
@slot('override')
    <fieldset>
        @component('components.radio', [
            'name' => 'acceptable_regions',
            'type' => 'checkbox',
            'objects' => $regions
        ])@endcomponent
    </fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'acceptable_programs',
    'label' => trans('camp.AcceptablePrograms'),
    'attributes' => 'required',
])
@slot('override')
    <fieldset>
        @component('components.radio', [
            'name' => 'acceptable_programs',
            'type' => 'checkbox',
            'objects' => $programs
        ])@endcomponent
    </fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'min_gpa',
    'label' => trans('camp.MinGPA'),
    'type' => 'number',
    'attributes' => 'step=any',
])@endcomponent

@component('components.input', [
    'name' => 'other_conditions',
    'label' => trans('camp.OtherConditions'),
    'textarea' => 1,
])@endcomponent

@component('components.input', [
    'name' => 'application_fee',
    'label' => trans('camp.ApplicationFee'),
    'type' => 'number',
])@endcomponent

@component('components.input', [
    'name' => 'url',
    'label' => trans('camp.URL'),
    'type' => 'url',
])@endcomponent

@component('components.input', [
    'name' => 'fburl',
    'label' => trans('camp.FBURL'),
    'type' => 'url',
])@endcomponent

@component('components.input', [
    'name' => 'app_open_date',
    'label' => trans('camp.AppOpenDate'),
    'type' => 'datetime-local',
])@endcomponent

@component('components.input', [
    'name' => 'app_close_date',
    'label' => trans('camp.AppCloseDate'),
    'type' => 'datetime-local',
])@endcomponent

@component('components.input', [
    'name' => 'reg_open_date',
    'label' => trans('camp.RegOpenDate'),
    'type' => 'datetime-local',
])@endcomponent

@component('components.input', [
    'name' => 'reg_close_date',
    'label' => trans('camp.RegCloseDate'),
    'type' => 'datetime-local',
])@endcomponent

@component('components.input', [
    'name' => 'event_start_date',
    'label' => trans('camp.EventStartDate'),
    'type' => 'datetime-local',
])@endcomponent

@component('components.input', [
    'name' => 'event_end_date',
    'label' => trans('camp.EventEndDate'),
    'type' => 'datetime-local',
])@endcomponent

<!-- TODO: Geolocation -->

@component('components.input', [
    'name' => 'quota',
    'label' => trans('camp.Quota'),
    'type' => 'number',
])@endcomponent

@stop
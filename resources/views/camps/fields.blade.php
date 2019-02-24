@section('camp-fields')

@component('components.input', [
    'name' => 'name_en',
    'label' => trans('camp.EnglishName'),
])
@endcomponent

@component('components.input', [
    'name' => 'name_th',
    'label' => trans('camp.ThaiName'),
])
@endcomponent

@component('components.input', [
    'name' => 'short_description_en',
    'label' => trans('camp.EnglishShortDescription'),
    'textarea' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'short_description_th',
    'label' => trans('camp.ThaiShortDescription'),
    'textarea' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'long_description',
    'label' => trans('camp.LongDescription'),
    'textarea' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'camp_category_id',
    'label' => trans('camp.Category'),
    'attributes' => 'required',
    'input_type' => 'radio',
    'objects' => $categories,
    'columns' => 4,
])
@endcomponent

<!-- TODO: client-side desc update -->
@component('components.input', [
    'name' => 'camp_procedure_id',
    'label' => trans('camp_procedure.CampProcedure'),
    'attributes' => 'required'.(!\Auth::user()->isAdmin() && isset($update) ? ' disabled' : ''),
    'input_type' => 'select',
    'objects' => $camp_procedures,
    'placeholder' => isset($update) ? null : 'Select Camp Application Type...',
    'desc' => isset($object) ? $object->camp_procedure->getDescription() : '',
])
@endcomponent

@php $can_list_organization = \Auth::user()->hasPermissionTo('organization-list'); @endphp

@component('components.input', [
    'name' => 'organization_id',
    'label' => trans('organization.Organization'),
    'attributes' => $can_list_organization ? 'required' : 'disabled',
    'input_type' => 'select',
    'objects' => $organizations,
    'placeholder' => $can_list_organization && !isset($update) ? 'Select Organization...' : null,
])
@endcomponent

@component('components.input',[
    'name' => 'acceptable_years',
    'label' => trans('camp.AcceptableYears'),
    'attributes' => 'required',
    'input_type' => 'checkbox',
    'objects' => $years,
])
@endcomponent

@component('components.input', [
    'name' => 'acceptable_regions',
    'label' => trans('camp.AcceptableRegions'),
    'attributes' => 'required',
    'input_type' => 'checkbox',
    'objects' => $regions,
])
@endcomponent

@component('components.input', [
    'name' => 'acceptable_programs',
    'label' => trans('camp.AcceptablePrograms'),
    'attributes' => 'required',
    'input_type' => 'checkbox',
    'objects' => $programs,
])
@endcomponent

@component('components.numeric_range', [
    'name' => 'min_cgpa',
    'label' => trans('camp.MinCGPA'),
    'min' => 1.0,
    'max' => 4.0,
    'step' => 0.01,
    'object' => isset($object) ? $object : null,
])
@endcomponent

@component('components.input', [
    'name' => 'other_conditions',
    'label' => trans('camp.OtherConditions'),
    'textarea' => 1,
])
@endcomponent

@component('components.input', [
    'name' => 'application_fee',
    'label' => trans('camp.ApplicationFee'),
    'type' => 'number',
])
@endcomponent

@component('components.input', [
    'name' => 'url',
    'label' => trans('camp.URL'),
    'type' => 'url',
])
@endcomponent

@component('components.input', [
    'name' => 'fburl',
    'label' => trans('camp.FBURL'),
    'type' => 'url',
])
@endcomponent

@component('components.input', [
    'name' => 'app_close_date',
    'label' => trans('camp.AppCloseDate'),
    'type' => 'datetime-local',
    'attributes' => 'required',
])
@endcomponent

@component('components.input', [
    'name' => 'event_start_date',
    'label' => trans('camp.EventStartDate'),
    'type' => 'datetime-local',
    'attributes' => 'required',
])
@endcomponent

@component('components.input', [
    'name' => 'event_end_date',
    'label' => trans('camp.EventEndDate'),
    'type' => 'datetime-local',
    'attributes' => 'required',
])
@endcomponent

<!-- TODO: Geolocation -->

@component('components.input', [
    'name' => 'quota',
    'label' => trans('camp.Quota'),
    'type' => 'number',
])
@endcomponent

@stop
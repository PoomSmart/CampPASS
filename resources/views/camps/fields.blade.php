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
    'name' => 'campcat_id',
    'label' => trans('camp.Category'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', [
        'name' => 'campcat_id',
        'objects' => $categories,
        'required' => 1
    ])@endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'cp_id',
    'label' => trans('camp.CampProcedure'),
    'attributes' => 'required',
])
@slot('override')
    @component('components.select', [
        'name' => 'cp_id',
    ])
    @endcomponent
@endslot
@endcomponent

@component('components.input', [
    'name' => 'org_id',
    'label' => trans('camp.Organization'),
    'attributes' => Auth::user()->hasPermissionTo('org-list') ? 'required' : '',
])
@slot('override')
    @component('components.select', [
        'name' => 'org_id',
        'disabled' => !Auth::user()->hasPermissionTo('org-list'),
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
    'name' => 'app_opendate',
    'label' => trans('camp.AppOpenDate'),
    'type' => 'date',
])@endcomponent

@component('components.input', [
    'name' => 'app_closedate',
    'label' => trans('camp.AppCloseDate'),
    'type' => 'date',
])@endcomponent

@component('components.input', [
    'name' => 'reg_opendate',
    'label' => trans('camp.RegOpenDate'),
    'type' => 'date',
])@endcomponent

@component('components.input', [
    'name' => 'reg_closedate',
    'label' => trans('camp.RegCloseDate'),
    'type' => 'date',
])@endcomponent

@component('components.input', [
    'name' => 'event_startdate',
    'label' => trans('camp.EventStartDate'),
    'type' => 'date',
])@endcomponent

@component('components.input', [
    'name' => 'event_enddate',
    'label' => trans('camp.EventEndDate'),
    'type' => 'date',
])@endcomponent

<!-- TODO: Geolocation -->

@component('components.input', [
    'name' => 'quota',
    'label' => trans('camp.Quota'),
    'type' => 'number',
])@endcomponent

@stop
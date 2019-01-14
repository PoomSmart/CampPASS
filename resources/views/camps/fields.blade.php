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
    'name' => 'short_description',
    'label' => trans('camp.ShortDescription'),
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'campcat_id',
    'label' => trans('camp.Category'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'campcat_id', 'objects' => $categories])
    @endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'org_id',
    'label' => trans('camp.Organization'),
    'attributes' => 'required',
])
@slot('override')
    <select id="org_id" class="form-control">
        @foreach ($organizations as $index => $org)
            <option>{{ $org }}</option>
        @endforeach
    </select>
@endslot
@endcomponent

@component('components.input', [
    'name' => 'required_programs',
    'label' => trans('camp.RequiredPrograms'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'required_programs', 'bit' => 1, 'type' => 'checkbox', 'objects' => $programs])
    @endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'min_gpa',
    'label' => trans('camp.MinGPA'),
    'type' => 'number',
])@endcomponent

@component('components.input', [
    'name' => 'other_conditions',
    'label' => trans('camp.OtherConditions'),
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
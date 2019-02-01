@section('camper-fields')

@component('components.input', [
    'name' => 'school_id',
    'label' => trans('account.School'),
    'attributes' => 'required',
])
@slot('override')
    <select name="school_id", id="school_id" class="form-control">
        @foreach ($schools as $index => $school)
            <option
                @if ($index == 0)
                    selected
                @endif
                value="{{ $school->id }}">{{ $school->getName() }}
            </option>
        @endforeach
    </select>
@endslot
@endcomponent

@component('components.input', [
    'name' => 'education_level',
    'label' => trans('account.EducationLevel'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', [
        'name' => 'education_level',
        'objects' => $education_levels,
        'getter' => 'name',
        'required' => 1
    ])
    @endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'cgpa',
    'label' => trans('account.CGPA'),
    'type' => 'number',
    'attributes' => 'required step=any',
])
@endcomponent

@component('components.input', [
    'name' => 'blood_group',
    'label' => trans('account.BloodGroup'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'blood_group', 'idx' => 1, 'objects' => ['A', 'O', 'B', 'AB'], 'required' => 1])
    @endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'guardian_name',
    'label' => trans('camper.GuardianName'),
    'attributes' => 'required',
])
@endcomponent

@component('components.input', [
    'name' => 'guardian_surname',
    'label' => trans('camper.GuardianSurname'),
    'attributes' => 'required',
])
@endcomponent

@component('components.input', [
    'name' => 'guardian_role',
    'label' => trans('camper.GuardianRole'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'guardian_role', 'idx' => 1, 'objects' => [trans('account.Father'), trans('account.Mother'), trans('app.Other')], 'required' => 1])
    @slot('append_last')
        @component('components.input', [
            'nowrapper' => 1,
            'name' => 'guardian_role_text',
            'class' => 'ml-2',
        ])
        @endcomponent
    @endslot
    @endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'guardian_mobile_no',
    'label' => trans('camper.GuardianMobileNo'),
    'attributes' => 'required',
    'type' => 'tel',
])
@endcomponent

@stop
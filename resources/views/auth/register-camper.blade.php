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
    'name' => 'mattayom',
    'label' => trans('account.Mattayom'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'mattayom', 'idx' => 1, 'objects' => ['1', '2', '3', '4', '5', '6'], 'required' => 1])
    @endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'cgpa',
    'label' => trans('account.CGPA'),
    'type' => 'number',
    'attributes' => 'required step=any',
])@endcomponent

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
    'label' => trans('account.GuardianName'),
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'guardian_role',
    'label' => trans('account.GuardianRole'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'guardian_role', 'idx' => 1, 'objects' => [trans('account.Father'), trans('account.Mother'), trans('account.Other')], 'required' => 1])
    @endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'guardian_mobile_no',
    'label' => trans('account.GuardianMobileNo'),
    'attributes' => 'required',
    'type' => 'tel',
])@endcomponent

@component('components.input', [
    'name' => 'short_biography',
    'label' => trans('account.ShortBiography'),
    'placeholder' => trans('account.YouCanWriteItLater'),
    'textarea' => 1,
    'attributes' => 'rows=3',
])@endcomponent

@stop
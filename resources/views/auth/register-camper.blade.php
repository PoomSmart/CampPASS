@section('camper-fields')

@component('components.input', [
    'name' => 'blood_group',
    'label' => trans('account.BloodGroup'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'blood_group', 'labels' => ['A', 'O', 'B', 'AB'], 'required' => '1'])@endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'guardian_name',
    'label' => trans('account.GuardianName'),
])@endcomponent

@component('components.input', [
    'name' => 'guardian_role',
    'label' => trans('account.GuardianRole'),
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'guardian_role', 'labels' => [trans('account.Father'), trans('account.Mother'), trans('account.Other')]])@endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'guardian_mobile_no',
    'label' => trans('account.GuardianMobileNo'),
    'type' => 'tel',
])@endcomponent

@component('components.input', [
    'name' => 'short_biography',
    'label' => trans('account.ShortBiography'),
    'placeholder' => trans('account.YouCanWriteItLater'),
    'textarea' => '1',
    'attributes' => 'rows=3',
])@endcomponent

@stop
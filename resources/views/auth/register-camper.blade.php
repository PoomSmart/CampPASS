@section('camper-fields')

@component('components.input', [
    'name' => 'bloodgroup',
    'label' => trans('account.BloodGroup'),
    'attributes' => 'required',
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'bloodgroup', 'labels' => ['A', 'O', 'B', 'AB'], 'required' => '1'])@endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'guardianname',
    'label' => trans('account.GuardianName'),
])@endcomponent

@component('components.input', [
    'name' => 'guardianrole',
    'label' => trans('account.GuardianRole'),
])
@slot('override')
<fieldset>
    @component('components.radio', ['name' => 'guardianrole', 'labels' => [trans('account.Father'), trans('account.Mother'), trans('account.Other')]])@endcomponent
</fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'guardianmobileno',
    'label' => trans('account.GuardianMobileNo'),
    'type' => 'tel',
])@endcomponent

@component('components.input', [
    'name' => 'shortbiography',
    'label' => trans('account.ShortBiography'),
    'placeholder' => trans('account.YouCanWriteItLater'),
    'textarea' => '1',
    'attributes' => 'rows=3',
])@endcomponent

@stop
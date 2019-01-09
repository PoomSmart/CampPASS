@section('basic-fields')

@component('components.input', [
    'name' => 'nameen',
    'label' => trans('account.EnglishName'),
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'surnameen',
    'label' => trans('account.EnglishSurname'),
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'nicknameen',
    'label' => trans('account.EnglishNickname'),
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'nameth',
    'label' => trans('account.ThaiName'),
])@endcomponent

@component('components.input', [
    'name' => 'surnameth',
    'label' => trans('account.ThaiSurname'),
])@endcomponent

@component('components.input', [
    'name' => 'nicknameth',
    'label' => trans('account.ThaiNickname'),
])@endcomponent

@component('components.input', [
    'name' => 'citizenid',
    'label' => trans('account.CitizenID'),
    'type' => 'number',
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'nationality',
    'label' => trans('account.Nationality'),
])
@slot('override')
    <fieldset>
        @component('components.radio', ['name' => 'nationality', 'labels' => [trans('account.Thai'), trans('account.Other')]])@endcomponent
    </fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'gender',
    'label' => trans('account.Gender'),
])
@slot('override')
    <fieldset>
        @component('components.radio', ['name' => 'gender', 'labels' => [trans('account.Male'), trans('account.Female'), trans('account.Other')]])@endcomponent
    </fieldset> 
@endslot
@endcomponent

@component('components.input', [
    'name' => 'mobileno',
    'label' => trans('account.MobileNo'),
    'type' => 'tel',
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'dob',
    'label' => trans('account.DOB'),
    'type' => 'date',
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'allergy',
    'label' => trans('account.Allergy'),
])@endcomponent

@component('components.input', [
    'name' => 'address',
    'label' => trans('account.Address'),
])@endcomponent

@component('components.input', [
    'name' => 'zipcode',
    'label' => trans('account.ZipCode'),
])@endcomponent

@component('components.input', [
    'name' => 'username',
    'label' => trans('account.Username'),
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'email',
    'label' => trans('account.Email'),
    'type' => 'email',
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'password',
    'label' => trans('account.Password'),
    'type' => 'password',
    'attributes' => 'required',
])@endcomponent

@component('components.input', [
    'name' => 'password_confirm',
    'label' => trans('account.ConfirmPassword'),
    'type' => 'password',
    'attributes' => 'required',
])@endcomponent

@stop
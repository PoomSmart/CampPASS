@section('basic-fields')

@component('components.input', [
    'name' => 'name_en',
    'label' => trans('account.EnglishName'),
])@endcomponent

@component('components.input', [
    'name' => 'surname_en',
    'label' => trans('account.EnglishSurname'),
])@endcomponent

@component('components.input', [
    'name' => 'nickname_en',
    'label' => trans('account.EnglishNickname'),
])@endcomponent

@component('components.input', [
    'name' => 'name_th',
    'label' => trans('account.ThaiName'),
])@endcomponent

@component('components.input', [
    'name' => 'surname_th',
    'label' => trans('account.ThaiSurname'),
])@endcomponent

@component('components.input', [
    'name' => 'nickname_th',
    'label' => trans('account.ThaiNickname'),
])@endcomponent

@component('components.input', [
    'name' => 'citizen_id',
    'label' => trans('account.CitizenID'),
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
    'name' => 'mobile_no',
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
    'placeholder' => trans('account.LeaveBlank'),
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
    'name' => 'password_confirmation',
    'label' => trans('account.ConfirmPassword'),
    'type' => 'password',
    'attributes' => 'required',
])@endcomponent

@stop
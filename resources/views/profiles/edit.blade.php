@extends('layouts.card')

@section('card_content')
    <h3>About You</h3>
    <div class="row">
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'name_th',
                'label' => trans('account.ThaiName'),
            ])
            @endcomponent
            @component('components.input', [
                'name' => 'name_en',
                'label' => trans('account.EnglishName'),
            ])
            @endcomponent
            @component('components.input', [
                'name' => 'nickname_th',
                'label' => trans('account.ThaiNickname'),
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'surname_th',
                'label' => trans('account.ThaiSurname'),
            ])
            @endcomponent
            @component('components.input', [
                'name' => 'surname_en',
                'label' => trans('account.EnglishSurname'),
            ])
            @endcomponent
            @component('components.input', [
                'name' => 'nickname_en',
                'label' => trans('account.EnglishNickname'),
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'dob',
                'label' => trans('account.DOB'),
                'type' => 'date',
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'citizen_id',
                'label' => trans('account.CitizenID'),
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'nationality',
                'label' => trans('account.Nationality'),
                'attributes' => 'required',
                'input_type' => 'radio',
                'objects' => [trans('account.Thai'), trans('app.Other')],
                'idx' => 1,

            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'gender',
                'label' => trans('account.Gender'),
                'attributes' => 'required',
                'input_type' => 'radio',
                'objects' => [trans('account.Male'), trans('account.Female'), trans('account.OtherGender')],
                'idx' => 1,
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'blood_group',
                'label' => trans('account.BloodGroup'),
                'attributes' => 'required',
                'input_type' => 'radio',
                'objects' => ['A', 'O', 'B', 'AB'],
                'idx' => 1,
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'religion_id',
                'label' => trans('account.Religion'),
                'attributes' => 'required',
                'input_type' => 'radio',
                'objects' => $religions,
            ])
            @endcomponent
        </div>
    </div>

    <h3>Education</h3>
    <div class="row">
        <div class="col-12">
            @component('components.input', [
                'name' => 'school_id',
                'label' => trans('account.School'),
                'attributes' => 'required',
                'input_type' => 'select',
                'objects' => $schools,
                'placeholder' => 'Select your school...',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'program_id',
                'label' => trans('camper.Program'),
                'attributes' => 'required',
                'input_type' => 'radio',
                'objects' => $programs,
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'education_level',
                'label' => trans('account.EducationLevel'),
                'attributes' => 'required',
                'input_type' => 'radio',
                'objects' => $education_levels,
                'getter' => 'name',
            ])
            @endcomponent
        </div>
    </div>

    <h3>Student Documents</h3>
    <div class="row">
        <div class="col-12">
            <!-- TODO: TBD -->
        </div>
    </div>

    <h3>Contact Information</h3>
    <div class="row">
        <div class="col-12">
            @component('components.input', [
                'name' => 'mobile_no',
                'label' => trans('account.MobileNo'),
                'type' => 'tel',
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'address',
                'label' => trans('account.StreetAddress'),
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'city',
                'label' => trans('account.City'),
                'attributes' => 'required',
                'input_type' => 'select',
                'objects' => [],
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'zipcode',
                'label' => trans('account.ZipCode'),
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
    </div>

    <h3>Emergency Contact Information</h3>
    <div class="row">
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'guardian_name',
                'label' => trans('camper.GuardianName'),
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'guardian_surname',
                'label' => trans('camper.GuardianSurname'),
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'education_level',
                'label' => trans('account.EducationLevel'),
                'attributes' => 'required',
                'input_type' => 'radio',
                'objects' => [trans('account.Father'), trans('account.Mother'), trans('app.Other')],
                'idx' => 1,
            ])
            @slot('append_last')
                @component('components.input', [
                    'name' => 'guardian_role_text',
                    'class' => 'ml-2',
                ])
                @endcomponent
            @endslot
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'guardian_mobile_no',
                'label' => trans('camper.GuardianMobileNo'),
                'attributes' => 'required',
                'type' => 'tel',
            ])
            @endcomponent
        </div>
    </div>
    
    <h3>Account</h3>
    <div class="row">
        <div class="col-12">
            @component('components.input', [
                'name' => 'username',
                'label' => trans('account.Username'),
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'email',
                'label' => trans('account.Email'),
                'type' => 'email',
                'attributes' => 'required',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'current_password',
                'label' => trans('account.CurrentPassword'),
                'type' => 'password',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'password',
                'label' => trans('account.Password'),
                'type' => 'password',
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'password_confirmation',
                'label' => trans('account.ConfirmPassword'),
                'type' => 'password',
            ])
            @endcomponent
        </div>
    </div>
@endsection
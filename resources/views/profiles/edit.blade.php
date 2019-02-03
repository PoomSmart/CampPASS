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
                'name' => 'gender',
                'label' => trans('account.Gender'),
                'attributes' => 'required',
            ])
            @slot('override')
                @component('components.radio', [
                    'name' => 'gender',
                    'idx' => 1,
                    'objects' => [trans('account.Male'), trans('account.Female'), trans('account.OtherGender')],
                    'required' => 1
                ])
                @endcomponent
            @endslot
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'blood_group',
                'label' => trans('account.BloodGroup'),
                'attributes' => 'required',
            ])
            @slot('override')
                @component('components.radio', [
                    'name' => 'blood_group',
                    'idx' => 1,
                    'objects' => ['A', 'O', 'B', 'AB'],
                    'required' => 1
                ])
                @endcomponent
            @endslot
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'religion_id',
                'label' => trans('account.Religion'),
                'attributes' => 'required',
            ])
            @slot('override')
                @component('components.radio', [
                    'name' => 'religion_id',
                    'objects' => $religions,
                    'required' => 1
                ])
                @endcomponent
            @endslot
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
            ])
            @slot('override')
                {!! Form::select('school_id', $schools, null, [
                    'class' => 'form-control',
                    'placeholder' => 'Select your school...',
                ]) !!}
            @endslot
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'program_id',
                'label' => trans('camper.Program'),
                'attributes' => 'required',
            ])
            @slot('override')
                @component('components.radio', [
                    'name' => 'program_id',
                    'objects' => $programs,
                    'required' => 1,
                ])
                @endcomponent
            @endslot
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'education_level',
                'label' => trans('account.EducationLevel'),
                'attributes' => 'required',
            ])
            @slot('override')
                @component('components.radio', [
                    'name' => 'education_level',
                    'objects' => $education_levels,
                    'getter' => 'name',
                    'required' => 1
                ])
                @endcomponent
            @endslot
            @endcomponent
        </div>
    </div>
@endsection
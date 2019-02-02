@extends('layouts.card')

@section('card_content')
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
                <fieldset>
                    @component('components.radio', ['name' => 'gender', 'idx' => 1, 'objects' => [trans('account.Male'), trans('account.Female'), trans('account.OtherGender')], 'required' => 1])
                    @endcomponent
                </fieldset> 
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
            <fieldset>
                @component('components.radio', ['name' => 'blood_group', 'idx' => 1, 'objects' => ['A', 'O', 'B', 'AB'], 'required' => 1])
                @endcomponent
            </fieldset> 
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
            <fieldset>
                @component('components.radio', ['name' => 'religion_id', 'objects' => $religions, 'required' => 1])
                @endcomponent
            </fieldset> 
            @endslot
            @endcomponent
        </div>
    </div>
@endsection
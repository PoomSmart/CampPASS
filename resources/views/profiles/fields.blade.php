@php
    $camper = $type == config('const.account.camper') ? 1 : null;
    $campmaker = $type == config('const.account.campmaker') ? 1 : null;
    $disabled = isset($disabled) && $disabled;
@endphp
<h3 class="mt-4">@lang('profile.About', [
        'entity' => !isset($object) || $object->id == auth()->user()->id ? trans('app.You') : $object->getFullName(),
    ])</h3>
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
                'required' => 1,
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'citizen_id',
                'label' => trans('account.CitizenID'),
                'required' => 1,
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'nationality',
                'label' => trans('account.Nationality'),
                'required' => 1,
                'input_type' => 'radio',
                'objects' => [
                    trans('account.Thai'),
                    trans('app.Other')
                ],
                'idx' => 1,
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'gender',
                'label' => trans('account.Gender'),
                'required' => 1,
                'input_type' => 'radio',
                'objects' => [
                    trans('account.Male'),
                    trans('account.Female'),
                    trans('account.OtherGender')
                ],
                'idx' => 1,
            ])
            @endcomponent
        </div>
        @if (isset($camper) || $disabled)
            <div class="col-md-6">
                @component('components.input', [
                    'name' => 'blood_group',
                    'label' => trans('account.BloodGroup'),
                    'required' => 1,
                    'input_type' => 'radio',
                    'objects' => ['A', 'O', 'B', 'AB'],
                    'idx' => 1,
                ])
                @endcomponent
            </div>
        @endif
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'religion_id',
                'label' => trans('account.Religion'),
                'required' => 1,
                'input_type' => 'radio',
                'objects' => $religions,
            ])
            @endcomponent
        </div>
    </div>

    @if ($camper || $disabled)
        <h3 class="mt-4">@lang('account.Education')</h3>
        <div class="row">
            <div class="col-12">
                @component('components.input', [
                    'name' => 'school_id',
                    'label' => trans('account.School'),
                    'required' => 1,
                    'input_type' => 'select',
                    'objects' => $schools,
                    'placeholder' => trans('profile.SelectYourSchool'),
                ])
                @endcomponent
            </div>
            <div class="col-12">
                @component('components.input', [
                    'name' => 'program_id',
                    'label' => trans('camper.Program'),
                    'required' => 1,
                    'input_type' => 'radio',
                    'objects' => $programs,
                ])
                @endcomponent
            </div>
            <div class="col-12">
                @component('components.input', [
                    'name' => 'education_level',
                    'label' => trans('account.EducationLevel'),
                    'required' => 1,
                    'input_type' => 'radio',
                    'objects' => $education_levels,
                    'getter' => 'name',
                    'radio_class' => 'mr-0',
                    'radio_attributes' => 'style=min-width:24%;',
                ])
                @endcomponent
            </div>
            <div class="col-12">
                @component('components.input', [
                    'name' => 'cgpa',
                    'label' => trans('camper.CGPA'),
                    'type' => 'number',
                    'required' => 1,
                    'no_form_control_class' => 1,
                    'attributes' => 'min=1.0 max=4.0 step=0.01 data-decimals=2',
                    'object' => isset($object) ? $object : null,
                ])
                @endcomponent
            </div>
        </div>

        @if (isset($update) || $disabled)
            <h3 class="mt-4">@lang('profile.StudentDocuments')</h3>
            <div class="row">
                <h4 class="col-12 mt-2">@lang('profile.Transcript')</h4>
                <div class="col-12">
                    @component('components.file_upload', [
                        'value' => trans('app.View'),
                        'args' => [
                            'user' => auth()->user()->id,
                            'type' => 'transcript',
                        ],
                        'upload' => !$disabled,
                        'download_route' => 'camp_application.document_download',
                        'delete_route' => $disabled ? null : 'camp_application.document_delete',
                        'full_width' => 1,
                        'name' => 'transcript',
                    ])
                    @endcomponent
                </div>
                <h4 class="col-12 mt-2">@lang('profile.StudentCertificate')</h4>
                <div class="col-12">
                    @component('components.file_upload', [
                        'value' => trans('app.View'),
                        'args' => [
                            'user' => auth()->user()->id,
                            'type' => 'certificate',
                        ],
                        'upload' => !$disabled,
                        'download_route' => 'camp_application.document_download',
                        'delete_route' => $disabled ? null : 'camp_application.document_delete',
                        'full_width' => 1,
                        'name' => 'certificate',
                    ])
                    @endcomponent
                </div>
            </div>
        @endif
    @endif

    @if ($campmaker)
        <h3 class="mt-4">@lang('campmaker.Organization')</h3>
        <div class="row">
            <div class="col-12">
                @component('components.input', [
                    'name' => 'organization_id',
                    'label' => trans('campmaker.Organization'),
                    'input_type' => 'select',
                    'objects' => $organizations,
                    'disabled' => isset($update),
                    'placeholder' => isset($update) ? null : trans('campmaker.SelectYourOrganization'),
                ])
                @endcomponent
            </div>
        </div>
    @endif

    <h3 class="mt-4">@lang('profile.ContactInformation')</h3>
    <div class="row">
        <div class="col-12">
            @component('components.input', [
                'name' => 'mobile_no',
                'label' => trans('account.MobileNo'),
                'type' => 'tel',
                'required' => 1,
            ])
            @endcomponent
        </div>
        <div class="col-12">
            @component('components.input', [
                'name' => 'street_address',
                'label' => trans('account.StreetAddress'),
                'required' => 1,
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'province_id',
                'label' => trans('account.Province'),
                'required' => 1,
                'input_type' => 'select',
                'objects' => $provinces,
                'placeholder' => trans('profile.SelectYourProvince'),
            ])
            @endcomponent
        </div>
        <div class="col-md-6">
            @component('components.input', [
                'name' => 'zipcode',
                'label' => trans('account.ZipCode'),
                'required' => 1,
            ])
            @endcomponent
        </div>
    </div>

    @if (isset($camper) || $disabled)
        <h3 class="mt-4">@lang('profile.EmergencyContactInformation')</h3>
        <div class="row">
            <div class="col-md-6">
                @component('components.input', [
                    'name' => 'guardian_name',
                    'label' => trans('camper.GuardianName'),
                    'required' => 1,
                ])
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.input', [
                    'name' => 'guardian_surname',
                    'label' => trans('camper.GuardianSurname'),
                    'required' => 1,
                ])
                @endcomponent
            </div>
            <div class="col-12">
                @component('components.input', [
                    'name' => 'guardian_role',
                    'label' => trans('camper.GuardianRole'),
                    'required' => 1,
                    'input_type' => 'radio',
                    'objects' => [
                        trans('account.Father'),
                        trans('account.Mother'),
                        trans('app.Other'),
                    ],
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
                    'required' => 1,
                    'type' => 'tel',
                ])
                @endcomponent
            </div>
        </div>
    @endif

    @if (!auth()->user() || auth()->user()->isCamper() || (!auth()->user()->isCamper() && !$disabled))
        <h3 class="mt-4">@lang('account.Account')</h3>
        <div class="row">
            <div class="col-md-6">
                @component('components.input', [
                    'name' => 'username',
                    'label' => trans('account.Username'),
                    'required' => 1,
                ])
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.input', [
                    'name' => 'email',
                    'label' => trans('account.Email'),
                    'type' => 'email',
                    'required' => 1,
                ])
                @endcomponent
            </div>
            @if (isset($update))
                <div class="col-12">
                    @component('components.input', [
                        'name' => 'current_password',
                        'label' => trans('account.CurrentPassword'),
                        'type' => 'password',
                    ])
                    @endcomponent
                </div>
            @endif
            <div class="col-md-6">
                @component('components.input', [
                    'name' => 'password',
                    'label' => trans('account.Password'),
                    'type' => 'password',
                    'required' => isset($update) ? null : 1,
                    'value' => '',
                ])
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.input', [
                    'name' => 'password_confirmation',
                    'label' => trans('account.ConfirmPassword'),
                    'type' => 'password',
                    'required' => isset($update) ? null : 1,
                ])
                @endcomponent
            </div>
        </div>
    @endif
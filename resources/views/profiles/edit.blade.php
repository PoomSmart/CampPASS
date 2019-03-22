@extends('layouts.card')

@php
    $readonly = isset($fields_disabled) && $fields_disabled;
    $not_readonly = !isset($fields_disabled) || isset($fields_disabled) && !$fields_disabled;
@endphp

@section('script')
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='cgpa']").inputSpinner();
            jQuery("#citizen_id,#gender,#dob").attr("readonly", "readonly");
            jQuery("input[name='blood_group'],input[name='gender']").attr("disabled", true);
        });
    </script>
    @if ($readonly)
        <script>
            jQuery(document).ready(function () {
                jQuery("#form").find("input,select").attr("disabled", true);
                jQuery("label").removeAttr("required");
            });
        </script>
        <script src="{{ asset('js/modal.js') }}"></script>
    @else
        <script src="{{ asset('js/check-unsaved.js') }}"></script>
    @endif
@endsection

@section('card_content')
    @if ($readonly)
        @component('components.dialog', [
            'confirm_type' => 'warning',
            'confirm_label' => trans('qualification.ReturnForm'),
            'title' => trans('qualification.ReturnFormTitle'),
        ])
        @slot('custom_body')
            <p>{{ trans('qualification.ReturnFormFieldsDescription') }}</p>
            @component('components.radio', [
                'name' => 'reasons',
                'type' => 'checkbox',
                'object' => null,
                'objects' => $return_reasons,
                'required' => 1,
                'idx' => 1,
                'radio_class' => 'w-100',
            ])
            @endcomponent
            @component('components.input', [
                'name' => 'remark',
                'label' => trans('qualification.Remark'),
                'textarea' => 1,
                'class' => 'h-auto',
            ])
            @endcomponent
        @endslot
        @endcomponent
    @endif
    <form id="form" method="POST" action="{{ route('profiles.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php $type = $user->type @endphp
        <div class="row">
            <div class="col-md-3">
                <div class="row justify-content-center">
                    <div class="container-fluid no-gutters text-center p-lg-4">
                        <img id="profile-preview" class="rounded-circle img-fluid w-100" src="{{ \App\Http\Controllers\ProfileController::profile_picture_path($user) }}"/>
                    </div>
                    @if ($not_readonly)
                        @component('components.profile_upload', [
                            'value' => trans('app.View'),
                            'args' => [
                                'user' => $user->id,
                            ],
                            'delete_route' => 'camp_application.profile_picture_delete',
                            'name' => 'profile',
                        ])
                        @endcomponent
                        <script>
                            document.getElementById("profile").onchange = function () {
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    document.getElementById("profile-preview").src = e.target.result;
                                };
                                reader.readAsDataURL(this.files[0]);
                            };
                        </script>
                    @endif
                </div>
            </div>
            <div class="col-md-9">
                <input name="type" type="hidden" value="{{ $type }}">
                @include('profiles.fields', [
                    'type' => $type,
                    'update' => 1,
                ])
                @if ($not_readonly)
                    <div class="text-center mt-4">
                        @component('components.submit', [
                            'label' => trans('app.Update'),
                            'class' => 'btn btn-info w-50',
                            'glyph' => 'far fa-save fa-xs',
                        ])
                        @endcomponent
                    </div>
                @endif
                @can('candidate-edit')
                    @if ($user->isCamper() && !isset($no_extra_button) || (isset($no_extra_button) && !$no_extra_button))
                        <div class="row text-center mt-4">
                            @php
                                $counter = $has_payment ? 1 : 0;
                                if (!$registration->confirmed() && !$registration->withdrawed())
                                    $counter += 2;
                                $col = 12 / $counter;
                            @endphp
                            @if ($col)
                                @if ($has_payment)
                                    <div class="col-md-{{ $col }} my-1 px-1">
                                        <a href="{{ route('camp_application.payment_download', $registration->id) }}" class="btn btn-secondary w-100"><i class="far fa-eye mr-1 fa-xs"></i>@lang('qualification.ViewPaymentSlip')</a>
                                    </div>
                                @endif
                                
                                @if (!$registration->confirmed() && !$registration->withdrawed())
                                    <div class="col-md-{{ $col }} my-1 px-1">
                                        <a href="{{ route('qualification.document_approve', $registration->id) }}" class="btn btn-success w-100" title={{ trans('qualification.ApproveFormFull') }}><i class="fas fa-check mr-1 fa-xs"></i>@lang('qualification.ApproveForm')</a>
                                    </div>
                                    <div class="col-md-{{ $col }} my-1 px-1">
                                        <button type="button" class="btn btn-warning w-100" title="{{ trans('qualification.ReturnFormFull') }}" data-action="{{ route('qualification.form_return', $form_score->id) }}" data-toggle="modal" data-target="#modal">
                                            <i class="fas fa-undo mr-1 fa-xs"></i>@lang('qualification.ReturnForm')
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </form>
@endsection
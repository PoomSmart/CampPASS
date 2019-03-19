@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='cgpa']").inputSpinner();
            jQuery("#citizen_id,#gender,#dob").attr("readonly", "readonly");
            jQuery("input[name='blood_group'],input[name='gender']").attr("disabled", true);
        });
    </script>
    @if (isset($disabled) && $disabled)
        <script>
            jQuery(document).ready(function () {
                jQuery('input:not(#transcript):not(#certificate)').attr('disabled', true);
            });
        </script>
    @else
        <script src="{{ asset('js/check-unsaved.js') }}"></script>
    @endif
@endsection

@section('card_content')
    <form id="form" method="POST" action="{{ route('profiles.update', $user) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php $type = $user->type @endphp
        <div class="row">
            <div class="col-md-3">
                <h3 class="mt-4">@lang('profile.ProfilePicture')</h3>
                <div class="row justify-content-center">
                    <div class="container-fluid no-gutters text-center p-lg-4">
                        <img id="profile-preview" class="rounded-circle img-fluid w-100" src="{{ \App\Http\Controllers\ProfileController::profile_picture_path($user) }}"/>
                    </div>
                    @if (!isset($disabled) || isset($disabled) && !$disabled)
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
                @if (!isset($disabled) || isset($disabled) && !$disabled)
                    <div class="text-center mt-4">
                        @component('components.submit', [
                            'label' => trans('app.Update'),
                            'class' => 'btn btn-primary w-50',
                            'glyph' => 'fas fa-pencil-alt fa-xs',
                        ])
                        @endcomponent
                    </div>
                @endif
                @can('candidate-edit')
                    @if ($user->isCamper() && !isset($no_extra_button) || (isset($no_extra_button) && !$no_extra_button))
                        <div class="text-center mt-4">
                            @if ($has_payment)
                                <a href="{{ route('camp_application.payment_download', $registration->id) }}" class="btn btn-secondary"><i class="far fa-eye mr-1 fa-xs"></i>@lang('qualification.ViewPaymentSlip')</a>
                            @endif
                            @if (!$registration->confirmed() && !$registration->withdrawed())
                                <a href="#" class="btn btn-success" title={{ trans('qualification.ApproveFormFull') }}>@lang('qualification.ApproveForm')</a>
                                <a href="{{ route('qualification.form_return', $form_score->id) }}" class="btn btn-warning" title={{ trans('qualification.ReturnFormFull') }}>@lang('qualification.ReturnForm')</a>
                            @endif
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </form>
@endsection
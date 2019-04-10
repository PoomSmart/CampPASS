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
                jQuery("#form").find("input,select,textarea").attr("disabled", true);
                jQuery("label").removeAttr("required");
                jQuery("[id*='desc-inline']").remove();
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
                        <small class="form-text text-muted mx-2">{{ trans('profile.PicDesc') }}</small>
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
            </div>
        </div>
    </form>
@endsection
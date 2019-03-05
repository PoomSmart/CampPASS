@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='cgpa']").inputSpinner();
        });
    </script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('card_content')
    <form id="form" method="POST" action="{{ route('profiles.update', \Auth::user()->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php $type = \Auth::user()->type @endphp
        <div class="row">
            <div class="col-md-3">
                <h3 class="mt-4">@lang('profile.ProfilePicture')</h3>
                <div class="row justify-content-center mt-4">
                    <div class="profile-header-container">
                        <div class="profile-header-img">
                            @php
                                $genders = [ 'M', 'F' ];
                                $gender = $genders[$user->gender % 2];
                            @endphp
                            <img class="rounded-circle" src="{{ asset("images/profiles/Profile_{$gender}.jpg") }}"/>
                        </div>
                    </div>
                    <!--<input type="file" class="form-control-file" name="avatar" id="avatarFile" aria-describedby="fileHelp">
                    <small id="fileHelp" class="form-text text-muted">Please upload a valid image file. Size of image should not be more than 2MB.</small>-->
                    <button class="btn btn-primary w-100 mt-3 mx-2">@lang('profile.UploadPicture')</button>
                    <button class="btn btn-danger w-100 mt-3 mx-2">@lang('profile.DeletePicture')</button>
                </div>
            </div>
            <div class="col-md-9">
                <input name="type" type="hidden" value="{{ $type }}">
                @include('profiles.fields', [
                    'type' => $type,
                    'update' => 1,
                ])
                <div class="text-center mt-4">
                    @component('components.submit', [
                        'label' => trans('app.Update'),
                        'class' => 'w-50',
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
    </form>
@endsection
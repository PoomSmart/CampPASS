@extends('layouts.card')

@section('card_content')
    <form method="POST" action="{{ route('profiles.update', \Auth::user()->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php $type = \Auth::user()->type @endphp
        <div class="row">
            <div class="col-md-3">
                <h3 class="mt-4">Profile Picture</h3>
                <div class="row justify-content-center mt-4">
                    <div class="profile-header-container">
                        <div class="profile-header-img">
                            <img class="rounded-circle" src="{{ asset('images/profiles/Profile.jpg') }}"/>
                        </div>
                    </div>
                    <!--<input type="file" class="form-control-file" name="avatar" id="avatarFile" aria-describedby="fileHelp">
                    <small id="fileHelp" class="form-text text-muted">Please upload a valid image file. Size of image should not be more than 2MB.</small>-->
                    <button class="btn btn-primary w-100 mt-3 mx-2">Upload Picture</button>
                    <button class="btn btn-danger w-100 mt-3 mx-2">Delete Picture</button>
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
                        'attributes' => 'w-50',
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
    </form>
@endsection
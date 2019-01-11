<?php if (empty($type)) {
    die("Internal error has occurred");
}?>

@extends('layouts.app')
@include('auth.register-basic')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ trans('account.Register') }}</div>

            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <input name="type" type="hidden" value="{{ $type }}">

                    @yield('basic-fields')
                    @if ($type == 1)
                        @include('auth.register-camper')
                        @yield('camper-fields')
                    @endif

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ trans('Register') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

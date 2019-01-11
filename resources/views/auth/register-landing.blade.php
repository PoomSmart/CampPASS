@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ trans('auth.WhoAreYou') }}</div>

            <div class="card-body">
                <ul>
                    <li><a href="{{ route('register-camper') }}">{{ trans('account.Camper') }}</a></li>
                    <li><a href="{{ route('register-campmaker') }}">{{ trans('account.CampMaker') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
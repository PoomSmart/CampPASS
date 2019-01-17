<?php if (empty($type)) {
    die("An internal error has occurred");
}?>

@extends('layouts.card')
@include('auth.register-basic')

@section('header')
    {{ trans('account.Register') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input name="type" type="hidden" value="{{ $type }}">
        @yield('basic-fields')
        @if ($type == config('const.account.camper'))
            @include('auth.register-camper')
            @yield('camper-fields')
        @elseif ($type == config('const.account.campmaker'))
            @component('components.input', [
                'name' => 'org_id',
                'label' => trans('campmaker.Organization'),
                'attributes' => 'required',
            ])
            @slot('override')
                <select name="org_id" id="org_id" class="form-control">
                    @foreach ($organizations as $index => $org)
                        <option
                            @if ($index == 0)
                                selected
                            @endif
                            value="{{ $org->id }}">{{ $org->getName() }}
                        </option>
                    @endforeach
                </select>
            @endslot
            @endcomponent
        @endif
        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ trans('Register') }}
                </button>
            </div>
        </div>
    </form>
@endsection
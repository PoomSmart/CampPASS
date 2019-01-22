<?php if (empty($type)) {
    die("An internal error has occurred");
}?>

@extends('layouts.card')
@include('auth.register-basic')

@section('header')
    {{ trans('account.Register') }}
@endsection

@section('card_content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input name="type" type="hidden" value="{{ $type }}">
        @yield('basic-fields')
        @if ($type == config('const.account.camper'))
            @include('auth.register-camper')
            @yield('camper-fields')
        @elseif ($type == config('const.account.campmaker'))
            @component('components.input', [
                'name' => 'organization_id',
                'label' => trans('campmaker.Organization'),
                'attributes' => 'required',
            ])
            @slot('override')
                <select name="organization_id" id="organization_id" class="form-control">
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
        @component('components.submit', ['label' => 'Register'])
        @endcomponent
    </form>
@endsection
@extends('layouts.card')

@section('card_content')
    <form method="POST" action="{{ route('profiles.update', \Auth::user()) }}">
        @csrf
        @method('PUT')
        @include('profiles.fields', [
            'type' => \Auth::user()->type,
            'update' => 1,
        ])
        @component('components.submit', [
            'label' => trans('app.Update'),
        ])
        @endcomponent
    </form>
@endsection
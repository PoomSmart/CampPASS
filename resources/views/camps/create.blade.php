@extends('layouts.card')
@include('camps.fields')

@section('header')
    @lang('camp.CreateCamp')
@endsection

@section('card_content')
    <form method="POST" action="{{ route('camps.store') }}">
        @csrf
        @yield('camp-fields')
        <div class="text-center mt-4">
            @component('components.submit', [
                'attributes' => 'w-50',
            ])
            @endcomponent
        </div>
    </form>
@endsection
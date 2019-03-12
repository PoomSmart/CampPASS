@extends('layouts.card')
@include('camps.fields')

@section('script')
    <script src="{{ asset('js/camp-fields.js') }}"></script>
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='min_cgpa'],input[name='quota']").inputSpinner();
        });
    </script>
@endsection

@section('header')
    @lang('camp.CreateCamp')
@endsection

@section('card_content')
    <form method="POST" action="{{ route('camps.store') }}">
        @csrf
        @yield('camp-fields')
        <div class="text-center mt-4">
            @component('components.submit', [
                'class' => 'w-50',
            ])
            @endcomponent
        </div>
    </form>
@endsection
@extends('layouts.card')
@include('camps.fields')

@section('script')
    <script src="{{ asset('js/camp-fields.js') }}"></script>
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='min_cgpa'],input[name='quota'],input[name='backup_limit'],input[name='application_fee'],input[name='deposit']").inputSpinner();
        });
    </script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    @lang('camp.CreateCamp')
@endsection

@section('card_content')
    <form method="POST" action="{{ route('camps.store') }}" enctype="multipart/form-data">
        @csrf
        @yield('camp-fields')
        <div class="text-center mt-4">
            @component('components.submit', [
                'class' => 'btn btn-primary w-50',
                'glyph' => 'fas fa-upload fa-xs'
            ])
            @endcomponent
        </div>
    </form>
@endsection
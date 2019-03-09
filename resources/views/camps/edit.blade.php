@extends('layouts.card')
@include('camps.fields', ['update' => 1])

@section('script')
    <script src="{{ asset('js/camp-fields.js') }}"></script>
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='min_cgpa'],input[name='quota']").inputSpinner();
            jQuery("[id^=camp_category_id]").attr("disabled", true);
        });
    </script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    @lang('app.Edit') {{ $object }}
@endsection

@section('card_content')
    <form id="form" action="{{ route('camps.update', $object->id) }}" method="POST">
        @csrf
        @method('PUT')
        @yield('camp-fields')
        <div class="mt-4 text-center">
            @component('components.submit', [
                'label' => trans('app.Update'),
                'class' => 'w-50',
            ])
            @endcomponent
        </div>
    </form>
@endsection
@extends('layouts.card')
@include('camps.fields', ['update' => 1])

@section('script')
    <script src="{{ asset('js/camp-fields.js') }}"></script>
    <script src="{{ asset('js/input-spinner.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("input[name='min_cgpa'],input[name='quota'],input[name='backup_limit']").inputSpinner();
            jQuery("[id^=camp_category_id],#camp_procedure_id,#organization_id").attr("disabled", true);
        });
    </script>
    <script src="{{ asset('js/check-unsaved.js') }}"></script>
@endsection

@section('header')
    @lang('app.Edit') {{ $object }}
@endsection

@section('card_content')
    <form id="form" action="{{ route('camps.update', $object->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @yield('camp-fields')
        <div class="mt-4 text-center">
            @component('components.submit', [
                'label' => trans('app.Update'),
                'class' => 'btn btn-primary w-50',
                'glyph' => 'fas fa-pencil-alt fa-xs',
            ])
            @endcomponent
        </div>
    </form>
@endsection
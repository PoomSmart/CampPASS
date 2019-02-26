@extends('layouts.card')
@include('camps.fields', ['update' => 1])

@section('script')
    <script src="{{ asset('js/checkbox-require.js') }}"></script>
@endsection

@section('header')
    @lang('app.Edit') {{ $object }}
@endsection

@section('card_content')
    <form action="{{ route('camps.update', $object->id) }}" method="POST">
        @csrf
        @method('PUT')
        @yield('camp-fields')
        <div class="mt-4 text-center">
            @component('components.submit', [
                'label' => trans('app.Update'),
                'attributes' => 'w-50',
            ])
            @endcomponent
        </div>
    </form>
@endsection
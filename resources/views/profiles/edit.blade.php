@extends('layouts.card')

@section('card_content')
    <form method="POST" action="{{ route('profiles.update', \Auth::user()) }}">
        @csrf
        @method('PUT')
        @php $type = \Auth::user()->type @endphp
        <input name="type" type="hidden" value="{{ $type }}">
        @include('profiles.fields', [
            'type' => $type,
            'update' => 1,
        ])
        <div class="text-center mt-4">
            @component('components.submit', [
                'label' => trans('app.Update'),
                'attributes' => 'w-50',
            ])
            @endcomponent
        </div>
    </form>
@endsection
@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/question.js') }}" defer></script>
@endsection

@section('header')
    {{ trans('CreateQuestion') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('questions.store') }}">
        @csrf
        <div id="questions"></div>
        @component('components.submit', ['label' => 'Submit'])
        @endcomponent
    </form>
    <div class="row justify-content-center">
        <div class="col-sm-12 form-group">
            <button class="btn btn-info" type="button" onclick="addQuestion();"><span>Add More Question</span></button>
        </div>
    </div>
@endsection
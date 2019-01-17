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
        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ trans('Submit') }}
                </button>
            </div>
        </div>
    </form>
    <div class="row justify-content-center">
        <div class="col-sm-12 col-md-10 form-group">
            <button class="btn btn-info" type="button" onclick="addQuestion();"><span>Add More Question</span></button>
        </div>
    </div>
@endsection
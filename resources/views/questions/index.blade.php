@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/question.js') }}" defer></script>
@endsection

@section('header')
    {{ trans('question.CreateQuestions') }}
@endsection

@section('card_content')
    <form method="POST" action="{{ route('questions.store') }}">
        @csrf
        <input name="camp_id" type="hidden" value="{{ $camp_id }}">
        <div id="questions">
            @component('questions.question', [
                'title' => 'Title',
                'label' => 'Question',
            ]);
            @endcomponent
        </div>
        @component('components.submit', ['label' => 'Save'])
        @slot('postcontent')
            <button class="btn btn-success" type="button" onclick="addQuestion();"><span>Add More Question</span></button>
        @endslot
        @endcomponent
    </form>
@endsection
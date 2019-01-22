@extends('layouts.card')

@section('header')
    Camp Application
@endsection

@section('content')
    @if (!$eligible)
        You are not eligible for this camp.
    @elseif ($quota_exceed)
        The quota is full.
    @elseif ($already_applied)
        You already applied for this camp.
    @else
        Questions.
        @foreach ($questions as $question)
            {{ $question }}
        @endforeach
    @endif
@endsection
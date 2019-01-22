@extends('layouts.card')

@section('script')
    <script src="{{ asset('js/answer.js') }}"></script>
@endsection

@section('header')
    Camp Application Form
@endsection

@section('card_content')
    @if (!$eligible)
        You are not eligible for this camp.
    @elseif ($quota_exceed)
        The quota is full.
    @elseif ($already_applied)
        You already applied for this camp.
    @else
        @if (!empty($json))
            <script>
                getCampId({!! $camp->id !!});
                var client_json = JSON.parse({!! $json !!});
                readJSON(client_json);
            </script>
        @endif
    @endif
@endsection
@extends('layouts.app')

@section('outer_content')
    {{ $user }}
    <a href="{{ route('profiles.edit', $user) }}" class="btn btn-primary">@lang('app.Edit')</a>
@endsection
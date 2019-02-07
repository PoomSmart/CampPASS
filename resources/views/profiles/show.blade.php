@extends('layouts.app')

@section('outer_content')
    {{ $user }}
    @role('camper')
        <a href="{{ route('profiles.edit', $user) }}" class="btn btn-primary">@lang('app.Edit')</a>
    @endrole
@endsection
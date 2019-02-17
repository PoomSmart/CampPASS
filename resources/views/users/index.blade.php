@extends('layouts.blank')

@section('header')
    @lang('account.ManageUsers')
@endsection

@section('content')
    <table class="table table-striped">
        <thead>
            <th class="align-middle">@lang('app.No_')</th>
            <th class="align-middle">@lang('account.Username')</th>
            <th class="align-middle">@lang('account.FullName')</th>
            <th class="align-middle">@lang('account.Email')</th>
            <th class="align-middle">@lang('account.Roles')</th>
            <th class="align-middle">@lang('app.Actions')</th>
        </thead>
        @foreach ($data as $key => $user)
            <tr>
                <th class="align-middle" scope="row">{{ ++$i }}</th>
                <th class="align-middle">{{ $user->username }}</th>
                <th class="align-middle"><a href="{{ route('profiles.show', $user) }}" target="_blank">{{ $user->getFullName() }}</a></th>
                <td class="align-middle">{{ $user->email }}</td>
                <td class="align-middle">
                    @if (!empty($user->getRoleNames()))
                        @foreach ($user->getRoleNames() as $v)
                            <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                    @endif
                </td>
                <td class="align-middle">
                    <a class="btn btn-info" href="{{ route('users.show', $user) }}">@lang('app.Show')</a>
                    <a class="btn btn-primary" href="{{ route('users.edit', $user) }}">@lang('app.Edit')</a>
                    {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $data->links() !!}
    </div>
@endsection
@extends('layouts.table')

@section('header')
    @lang('account.ManageUsers')
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>@lang('app.No_')</th>
            <th>@lang('account.Username')</th>
            <th>@lang('account.FullName')</th>
            <th>@lang('account.Email')</th>
            <th>@lang('account.Roles')</th>
            <th>@lang('app.Actions')</th>
        </tr>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $user->username }}</td>
                <td><a href="{{ route('profiles.show', $user) }}" target="_blank">{{ $user->getFullName() }}</a></td>
                <td>{{ $user->email }}</td>
                <td>
                    @if (!empty($user->getRoleNames()))
                        @foreach ($user->getRoleNames() as $v)
                            <label class="badge badge-success">{{ $v }}</label>
                        @endforeach
                    @endif
                </td>
                <td>
                    <a class="btn btn-info" href="{{ route('users.show', $user->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route('users.edit', $user) }}">@lang('app.Edit')</a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    {!! $data->render() !!}
@endsection
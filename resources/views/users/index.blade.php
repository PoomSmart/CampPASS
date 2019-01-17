@extends('layouts.table')

@section('header')
    {{ trans('UsersManagement') }}
@endsection

@section('button')
    <a class="btn btn-success" href="{{ route('users.create') }}">{{ trans('account.CreateUser') }}</a>
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>{{ trans('app.No_') }}</th>
            <th>{{ trans('account.Username') }}</th>
            <th>{{ trans('account.FullName') }}</th>
            <th>{{ trans('account.Email') }}</th>
            <th>Roles</th>
            <th width="280px">{{ trans('app.Actions') }}</th>
        </tr>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->getFullName() }}</td>
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
                    <a class="btn btn-primary" href="{{ route('users.edit', $user) }}">{{ trans('app.Edit') }}</a>
                        {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    {!! $data->render() !!}
@endsection
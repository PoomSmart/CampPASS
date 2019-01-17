@extends('layouts.table')

@section('header')
    {{ trans('RoleManagement') }}
@endsection

@section('button')
    <a class="btn btn-success" href="{{ route('roles.create') }}">{{ trans('account.CreateRole') }}</a>
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>{{ trans('app.No_') }}</th>
            <th>{{ trans('app.Name') }}</th>
            <th width="280px">Actions</th>
        </tr>
            @foreach ($roles as $key => $role)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a class="btn btn-info" href="{{ route('roles.show', $role->id) }}">{{ trans('app.Show') }}</a>
                        @can('role-edit')
                            <a class="btn btn-primary" href="{{ route('roles.edit', $role->id) }}">{{ trans('app.Edit') }}</a>
                        @endcan
                        @can('role-delete')
                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style'=>'display:inline']) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        @endcan
                    </td>
                </tr>
            @endforeach
    </table>
    {!! $roles->render() !!}
@endsection
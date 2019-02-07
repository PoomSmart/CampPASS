@extends('layouts.table')

@section('header')
    @lang('account.ManageRoles')
@endsection

@section('extra-buttons')
    <a class="btn btn-success" href="{{ route('roles.create') }}">@lang('account.CreateRole')</a>
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>@lang('app.No_')</th>
            <th>@lang('app.Name')</th>
            <th width="250px">@lang('app.Actions')</th>
        </tr>
            @foreach ($roles as $key => $role)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        <a class="btn btn-info" href="{{ route('roles.show', $role->id) }}">@lang('app.Show')</a>
                        @can('role-edit')
                            <a class="btn btn-primary" href="{{ route('roles.edit', $role->id) }}">@lang('app.Edit')</a>
                        @endcan
                        @can('role-delete')
                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        @endcan
                    </td>
                </tr>
            @endforeach
    </table>
    {!! $roles->render() !!}
@endsection
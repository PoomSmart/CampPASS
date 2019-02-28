@extends('layouts.blank')

@section('header')
    @lang('account.ManageRoles')
@endsection

@section('extra-buttons')
    <a class="btn btn-success w-50" href="{{ route('roles.create') }}">@lang('account.CreateRole')</a>
@endsection

@section('content')
    <table class="table table-striped">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('app.Name')</th>
            <th width="250px">@lang('app.Actions')</th>
        </thead>
            @foreach ($roles as $key => $role)
                <tr>
                    <th scope="row">{{ ++$i }}</th>
                    <th>{{ $role->name }}</th>
                    <td>
                        <a class="btn btn-info" href="{{ route('roles.show', $role->id) }}">@lang('app.View')</a>
                        @can('role-edit')
                            <a class="btn btn-primary" href="{{ route('roles.edit', $role->id) }}">@lang('app.Edit')</a>
                        @endcan
                        @can('role-delete')
                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                                {!! Form::submit(trans('app.Delete'), ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        @endcan
                    </td>
                </tr>
            @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $roles->links() !!}
    </div>
@endsection
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
            <th class="fit">@lang('app.No_')</th>
            <th>@lang('app.Name')</th>
            <th>@lang('app.Actions')</th>
        </thead>
            @foreach ($roles as $key => $role)
                <tr>
                    <th scope="row">{{ ++$i }}</th>
                    <th>{{ $role->name }}</th>
                    <td class="fit">
                        <a class="btn btn-primary" href="{{ route('roles.show', $role->id) }}"><i class="far fa-eye mr-1 fa-xs"></i>@lang('app.View')</a>
                        @can('role-edit')
                            <a class="btn btn-info" href="{{ route('roles.edit', $role->id) }}"><i class="fas fa-pencil-alt mr-1 fa-xs"></i>@lang('app.Edit')</a>
                        @endcan
                        
                        @can('role-delete')
                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                            @component('components.submit', [
                                'label' => trans('app.Delete'),
                                'class' => 'btn btn-danger',
                                'glyph' => 'fas fa-trash fa-xs',
                            ])
                            @endcomponent
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
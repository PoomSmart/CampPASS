@extends('layouts.blank')

@section('header')
    @lang('account.ManageRoles')
@endsection

@section('extra-buttons')
    @component('components.a', [
        'class' => 'btn btn-success',
        'href' => route('roles.create'),
        'label' => trans('account.CreateRole'),
        'auto_width' => 1,
    ])
    @endcomponent
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
                        <a class="btn btn-secondary btn-sm" href="{{ route('roles.show', $role->id) }}"><i class="far fa-eye mr-1 fa-xs"></i>@lang('app.View')</a>
                        @can('role-edit')
                            <a class="btn btn-info btn-sm" href="{{ route('roles.edit', $role->id) }}"><i class="fas fa-pencil-alt mr-1 fa-xs"></i>@lang('app.Edit')</a>
                        @endcan
                        @can('role-delete')
                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                            @component('components.submit', [
                                'label' => trans('app.Delete'),
                                'class' => 'btn btn-danger btn-sm',
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
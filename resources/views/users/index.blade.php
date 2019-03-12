@extends('layouts.blank')

@section('header')
    @lang('account.ManageUsers')
@endsection

@section('content')
    <div class="d-flex justify-content-center">
        {!! $data->links() !!}
    </div>
    <table class="table table-striped">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('account.Username')</th>
            <th>@lang('account.FullName')</th>
            <th>@lang('account.Email')</th>
            <th class="fit">@lang('account.Roles')</th>
            <th>@lang('app.Activated')</th>
            <th>@lang('app.Actions')</th>
        </thead>
        @foreach ($data as $key => $user)
            <tr>
                <th scope="row">{{ ++$i }}</th>
                <th>{{ $user->username }}</th>
                <th class="fit"><a href="{{ route('profiles.show', $user->id) }}" target="_blank">{{ $user->getFullName() }}</a></th>
                <td>{{ $user->email }}</td>
                <td>
                    @if (!empty($user->getRoleNames()))
                        @foreach ($user->getRoleNames() as $v)
                            <label class="badge badge-success">{{ trans("account.{$v}") }}</label>
                        @endforeach
                    @endif
                </td>
                <td class="text-center{{ $user->isActivated() ? ' table-success text-success' : ' table-danger text-danger' }}">{{ $user->isActivated() ? trans('app.Yes') : trans('app.No') }}</td>
                <td class="fit">
                    <a class="btn btn-info" href="{{ route('users.show', $user->id) }}">@lang('app.View')</a>
                    <a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}">@lang('app.Edit')</a>
                    {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                        {!! Form::submit(trans('app.Delete'), ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $data->links() !!}
    </div>
@endsection
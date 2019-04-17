@extends('layouts.blank')

@section('header')
    @lang('notification.Notifications')
@endsection

@section('content')
    <div class="d-flex justify-content-center">
        {!! $notifications->links() !!}
    </div>
    <table class="table table-striped table-static">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('notification.Notifications')</th>
            <th>@lang('notification.Type')</th>
        </thead>
	    @foreach ($notifications as $notification)
            <tr>
                <th scope="row">{{ ++$i }}</th>
                <td><a href="{{ $notification->data['url'] }}?read={{ $notification->id }}">{{ $notification->data['content'][app()->getLocale()] }}</a></td>
                <td>{{ \App\Common::readableNotificationType($notification->type) }}</td>
            </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $notifications->links() !!}
    </div>
@endsection
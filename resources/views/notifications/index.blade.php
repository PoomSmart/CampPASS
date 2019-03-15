@extends('layouts.blank')

@section('header')
    @lang('notification.Notifications')
@endsection

@section('content')
    <div class="d-flex justify-content-center">
        {!! $notifications->links() !!}
    </div>
    <table class="table table-striped">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('notification.Content')</th>
            <th>@lang('app.Actions')</th>
        </thead>
	    @foreach ($notifications as $notification)
            <tr>
                <th scope="row">{{ ++$i }}</th>
                <td class="text-truncate"><a href="{{ $notification->data['url'] }}">{{ $notification->data['content'][app()->getLocale()] }}</a></td>
                <td>
                    {{-- TODO: Mark as read (AJAX?) --}}
                </td>
            </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $notifications->links() !!}
    </div>
@endsection
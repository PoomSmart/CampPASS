@extends('layouts.table')

@section('header')
    Camp Browser
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>{{ trans('app.No_') }}</th>
            <th>{{ trans('app.LocalizedName') }}</th>
            <th>{{ trans('camp.ShortDescription') }}</th>
            <th>{{ trans('Application') }}</th>
        </tr>
	    @foreach ($camps as $camp)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $camp->getName() }}</td>
            <td>{{ $camp->getShortDescription() }}</td>
            <td>
                @role('camper')
                    <a class="btn btn-primary" href="{{ route('camp_application.landing', $camp->id) }}">{{ trans('Apply') }}</a>
                @endrole
            </td>
	    </tr>
	    @endforeach
    </table>
    {!! $camps->links() !!}
@endsection
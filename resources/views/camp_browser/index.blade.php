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
        </tr>
	    @foreach ($camps as $camp)
	    <tr>
	        <td>{{ ++$i }}</td>
            <td><a href="{{ route('camps.show', $camp->id) }}">{{ $camp->getName() }}</a></td>
            <td>{{ $camp->getShortDescription() }}</td>
	    </tr>
	    @endforeach
    </table>
    {!! $camps->links() !!}
@endsection
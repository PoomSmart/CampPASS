@extends('layouts.table')

@section('header')
    Camps
@endsection

@can('camp-create')
    @section('button')
        <a class="btn btn-success" href="{{ route('camps.create') }}">{{ trans('camp.CreateCamp') }}</a>
    @endsection
@endcan

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>{{ trans('app.No_') }}</th>
            <th>{{ trans('app.LocalizedName') }}</th>
            <th>{{ trans('camp.ShortDescription') }}</th>
            <th>Registered Campers</th>
            <th>{{ trans('camp.Status') }}</th>
            <th width="280px">{{ trans('app.Actions') }}</th>
        </tr>
	    @foreach ($camps as $camp)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $camp->getName() }}</td>
            <td>{{ $camp->getShortDescription() }}</td>
            <td>{{ $camp->approved ? $camp->campers()->count() : 0 }}</td>
            <td>{{ $camp->approved ? trans('camp.Approved') : trans('camp.ApprovalPending') }}</td>
	        <td>
                @if (!$camp->approved)
                    @can('camp-approve')
                        <form action="{{ route('camps.approve', $camp->id) }}" method="PATCH">
                            @csrf
                            <button type="submit" class="btn btn-warning">{{ trans('app.Approve') }}</button>
                        </form>
                    @endcan
                @else
                    @can('question-list')
                        <a class="btn btn-info" href="{{ route('questions.show', $camp->id) }}">{{ trans('question.Question') }}</a>
                    @endcan
                @endif
                <form action="{{ route('camps.destroy', $camp->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('camps.show', $camp->id) }}">{{ trans('app.Show') }}</a>
                    @can('camp-edit')
                        <a class="btn btn-primary" href="{{ route('camps.edit', $camp->id) }}">{{ trans('app.Edit') }}</a>
                    @endcan
                    @can('camp-delete') <!-- TODO: Add confirmation dialog + Grant from admin -->
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ trans('Delete') }}</button>
                    @endcan
                </form>
            </td>
	    </tr>
	    @endforeach
    </table>
    {!! $camps->links() !!}
@endsection
@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Camps</h2>
            </div>
            <div class="pull-right">
                @can('camp-create')
                <a class="btn btn-success" href="{{ route('camps.create') }}">{{ trans('camp.CreateCamp') }}</a>
                @endcan
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>{{ trans('app.No_') }}</th>
            <th>Localized Name</th>
            <th>{{ trans('camp.ShortDescription') }}</th>
            <th>{{ trans('camp.Approved') }}</th>
            <th width="280px">{{ trans('app.Actions' ) }}</th>
        </tr>
	    @foreach ($camps as $camp)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $camp->getName() }}</td>
            <td>{{ $camp->short_description }}</td>
            <td>{{ $camp->approved }}</td>
	        <td>
                <form action="{{ route('camps.destroy', $camp->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('camps.show', $camp->id) }}">{{ trans('app.Show') }}</a>
                    @can('camp-edit')
                    <a class="btn btn-primary" href="{{ route('camps.edit', $camp->id) }}">{{ trans('app.Edit') }}</a>
                    @endcan

                    @csrf
                    @method('DELETE')
                    @can('camp-delete')
                    <button type="submit" class="btn btn-danger">{{ trans('Delete') }}</button>
                    @endcan
                </form>
            </td>
	    </tr>
	    @endforeach
    </table>

    {!! $camps->links() !!}
@endsection
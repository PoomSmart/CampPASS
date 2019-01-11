@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Camps</h2>
            </div>
            <div class="pull-right">
                @can('camp-create')
                <a class="btn btn-success" href="{{ route('camps.create') }}">Create New Camp</a>
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
            <th>No</th>
            <th>Name</th>
            <th>Details</th>
            <th width="280px">Action</th>
        </tr>
	    @foreach ($camps as $camp)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td>{{ $camp->nameen }}</td>
	        <td>{{ $camp->shortdescription }}</td>
	        <td>
                <form action="{{ route('camps.destroy',$camp->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('camps.show',$camp->id) }}">Show</a>
                    @can('camp-edit')
                    <a class="btn btn-primary" href="{{ route('camps.edit',$camp->id) }}">Edit</a>
                    @endcan


                    @csrf
                    @method('DELETE')
                    @can('camp-delete')
                    <button type="submit" class="btn btn-danger">Delete</button>
                    @endcan
                </form>
	        </td>
	    </tr>
	    @endforeach
    </table>

    {!! $camps->links() !!}
@endsection
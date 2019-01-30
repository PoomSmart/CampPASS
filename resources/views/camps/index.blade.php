@extends('layouts.table')

@section('header')
    Camps
@endsection

@can('camp-create')
    @section('extra-buttons')
        <a class="btn btn-success" href="{{ route('camps.create') }}">{{ trans('camp.CreateCamp') }}</a>
    @endsection
@endcan

@section('content')
    @component('components.dialog', [
        'title' => 'Confirmation',
        'body' => 'Are you sure you want to delete this camp?',
        'confirm_label' => 'Confirm',
        'confirm_type' => 'danger',
        'form_type' => 'DELETE',
    ])
    @endcomponent
    <script>
        jQuery("#modal").on("show.bs.modal", function (event) {
            var target = jQuery(event.relatedTarget);
            var form_action = target.attr("data-action");
            jQuery(this).find("#confirm-form").attr("action", form_action);
        });
    </script>
    <table class="table table-bordered">
        <tr>
            <th>{{ trans('app.No_') }}</th>
            <th>{{ trans('app.LocalizedName') }}</th>
            <th>{{ trans('camp.ShortDescription') }}</th>
            <th>Registered Campers</th>
            <th>{{ trans('camp.Status') }}</th>
            <th width="240px">{{ trans('app.Actions') }}</th>
        </tr>
	    @foreach ($camps as $camp)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td><a href="{{ route('camps.show', $camp->id) }}">{{ $camp->getName() }}</a></td>
            <td>{{ $camp->getShortDescription() }}</td>
            <td>{{ $camp->approved ? $camp->campers(null)->count() : 0 }}</td>
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
                    @can('question-edit')
                        <a class="btn btn-info" href="{{ route('questions.show', $camp->id) }}">{{ trans('question.Question') }}</a>
                    @endcan
                @endif
                @can('camp-edit')
                    <a class="btn btn-primary" href="{{ route('camps.edit', $camp->id) }}">{{ trans('app.Edit') }}</a>
                @endcan
                @can('camp-delete')
                    <button type="button" class="btn btn-danger" data-action="{{ route('camps.destroy', $camp->id) }}" data-toggle="modal" data-target="#modal">
                        {{ trans('app.Delete') }}
                    </button>
                @endcan
            </td>
	    </tr>
        @endforeach
    </table>
    {!! $camps->links() !!}
@endsection
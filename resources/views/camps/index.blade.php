@extends('layouts.blank')

@section('header')
    @lang('camp.Camps')
@endsection

@section('script')
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection

@can('camp-create')
    @section('extra-buttons')
        <a class="btn btn-success" href="{{ route('camps.create') }}">@lang('camp.CreateCamp')</a>
    @endsection
@endcan

@section('content')
    @component('components.dialog', [
        'body' => 'Are you sure you want to delete this camp?',
        'confirm_type' => 'danger',
        'method' => 'DELETE',
    ])
    @endcomponent
    <table class="table table-bordered">
        <tr>
            <th>@lang('app.No_')</th>
            <th>@lang('camp.Name')</th>
            <th>@lang('camp.ShortDescription')</th>
            <th>@lang('camper.RegisteredCampers')</th>
            <th>@lang('camp.Status')</th>
            <th>Grading Type</th>
            <th width="240px">@lang('app.Actions')</th>
        </tr>
	    @foreach ($camps as $camp)
	    <tr>
	        <td>{{ ++$i }}</td>
	        <td><a href="{{ route('camps.show', $camp->id) }}">{{ $camp }}</a></td>
            <td>{{ $camp->getShortDescription() }}</td>
            <?php
                if ($camp->approved) {
                    $registration_count = $camp->campers(null)->count();
                    if ($camp->quota)
                        $registration_count = "{$registration_count} / {$camp->quota}";
                } else
                    $registration_count = 0;
            ?>
            <td>{{ $registration_count }}</td>
            <td>{{ $camp->approved ? trans('camp.Approved') : trans('camp.ApprovalPending') }}</td>
            <td>{{ $camp->gradingType() }}</td>
	        <td>
                @if (!$camp->approved)
                    @can('camp-approve')
                        <form action="{{ route('camps.approve', $camp->id) }}" method="PATCH">
                            @csrf
                            <button type="submit" class="btn btn-warning">@lang('app.Approve')</button>
                        </form>
                    @endcan
                @else
                    @can('question-edit')
                        @if ($camp->camp_procedure()->candidate_required)
                            <a class="btn btn-info" href="{{ route('questions.show', $camp->id) }}">@lang('question.Question')</a>
                        @endif
                    @endcan
                @endif
                @can('camp-edit')
                    <a class="btn btn-primary" href="{{ route('camps.edit', $camp->id) }}">@lang('app.Edit')</a>
                @endcan
                @can('camp-delete')
                    <button type="button" class="btn btn-danger" data-action="{{ route('camps.destroy', $camp->id) }}" data-toggle="modal" data-target="#modal">
                        @lang('app.Delete')
                    </button>
                @endcan
            </td>
	    </tr>
        @endforeach
    </table>
    {!! $camps->links() !!}
@endsection
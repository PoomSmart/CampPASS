@extends('layouts.blank')

@section('header')
    @lang('camp.Camps')
@endsection

@section('script')
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection

@can('camp-create')
    @section('extra-buttons')
        <a class="btn btn-success w-50" href="{{ route('camps.create') }}">@lang('camp.CreateCamp')</a>
    @endsection
@endcan

@section('content')
    @component('components.dialog', [
        'body' => 'Are you sure you want to delete this camp?',
        'confirm_type' => 'danger',
        'method' => 'DELETE',
    ])
    @endcomponent
    <div class="d-flex justify-content-center">
        {!! $camps->links() !!}
    </div>
    <table class="table table-striped">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('camp.Name')</th>
            <th>@lang('registration.ApplicationForms')</th>
            <th>@lang('camp.GradingType')</th>
            <th>@lang('camp.Status')</th>
            <th>@lang('qualification.IsAnnounced')</th>
            <th width="250px">@lang('app.Actions')</th>
        </thead>
	    @foreach ($camps as $camp)
	    <tr>
	        <th scope="row">{{ ++$i }}</th>
	        <th><a href="{{ route('camps.show', $camp->id) }}">{{ $camp }}</a></th>
            @php
                if ($camp->approved) {
                    $registration_count = $camp->campers()->count();
                    if ($camp->quota)
                        $registration_count = "{$registration_count} / {$camp->quota}";
                } else
                    $registration_count = 0;
            @endphp
            <td><a href="{{ route('camps.registration', $camp->id) }}">{{ $registration_count }}</a></td>
            <td class="text-muted">{{ $camp->gradingType() }}</td>
            <td class="text-center{{ $camp->approved ? ' text-success table-success' : ' table-warning' }}">{{ $camp->approved ? trans('camp.Approved') : trans('camp.ApprovalPending') }}</td>
            <td class="text-center{{ $camp->question_set ? ($camp->question_set->announced ? ' text-success table-success' : ' table-danger') : null }}">{{ $camp->question_set ? ($camp->question_set->announced ? trans('app.Yes') : trans('app.No')) : 'N/A' }}</td>
            <td>
                @if (!$camp->approved)
                    @can('camp-approve')
                        <form class="d-inline-block" action="{{ route('camps.approve', $camp->id) }}" method="PATCH">
                            @csrf
                            <button type="submit" class="btn btn-warning">@lang('app.Approve')</button>
                        </form>
                    @endcan
                @else
                    @can('question-edit')
                        @if ($camp->camp_procedure->candidate_required)
                            <a class="btn btn-info" href="{{ route('questions.show', $camp->id) }}">@lang('question.Questions')</a>
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
    <div class="d-flex justify-content-center">
        {!! $camps->links() !!}
    </div>
@endsection
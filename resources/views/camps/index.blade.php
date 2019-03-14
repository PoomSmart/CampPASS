@extends('layouts.blank')

@section('header')
    @lang('camp.Camps')
@endsection

@section('script')
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection

@section('custom-width')
    <div class="col-12">
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
            <th>@lang('camp_procedure.CampProcedure')</th>
            <th>@lang('camp.GradingType')</th>
            <th>@lang('camp.Status')</th>
            <th>@lang('qualification.IsAnnounced')</th>
            <th>@lang('app.Actions')</th>
        </thead>
	    @foreach ($camps as $camp)
	    <tr>
	        <th scope="row">{{ ++$i }}</th>
	        <td class="fit"><a href="{{ route('camps.show', $camp->id) }}">{{ $camp }}</a></th>
            @php
                if ($camp->approved) {
                    $registration_count = $camp->campers()->count();
                    if ($camp->quota)
                        $registration_count = "{$registration_count} / {$camp->quota}";
                } else
                    $registration_count = 0;
                $question_set = $camp->question_set;
            @endphp
            <td class="fit"><a href="{{ $question_set && $question_set->announced ? route('qualification.candidate_result', $question_set->id) : route('camps.registration', $camp->id) }}">{{ $registration_count }}</a></td>
            <td class="text-muted">
                @foreach ($camp->camp_procedure->getTags() as $tag)
                    <label class="badge badge-success font-weight-normal">{{ $tag }}</label>
                @endforeach
            </td>
            <td class="text-muted fit">{{ $camp->gradingType() }}</td>
            <td class="text-center fit{{ $camp->approved ? ' text-success table-success' : ' table-warning' }}">{{ $camp->approved ? trans('camp.Approved') : trans('camp.ApprovalPending') }}</td>
            <td class="text-center{{ $question_set ? ($question_set->announced ? ' text-success table-success' : ' text-danger table-danger') : null }}">{{ $question_set ? ($question_set->announced ? trans('app.Yes') : trans('app.No')) : trans('app.N/A') }}</td>
            <td class="fit">
                @if (!$camp->approved)
                    @can('camp-approve')
                        <a class="btn btn-warning" href="{{ route('camps.approve', $camp->id) }}">@lang('app.Approve')</a>
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
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
        <a class="btn btn-success w-50" href="{{ route('camps.create') }}"><i class="fas fa-plus mr-2 fa-xs"></i>@lang('camp.CreateCamp')</a>
    @endsection
@endcan

@section('content')
    @component('components.dialog', [
        'body' => trans('camp.SureDeleteCamp'),
        'confirm_type' => 'danger',
        'method' => 'DELETE',
    ])
    @endcomponent
    <div class="d-flex justify-content-center">
        {!! $camps->links() !!}
    </div>
    <h5>
        <span class="badge badge-success font-weight-normal"><i class="fas fa-check fa-xs mr-1"></i>@lang('camp.Approved')</span>
        <span class="badge badge-success font-weight-normal"><i class="fas fa-bullhorn fa-xs mr-1"></i>@lang('qualification.Announced')</span>
        <span class="badge badge-warning font-weight-normal"><i class="far fa-clock fa-xs mr-1"></i>@lang('camp.ApprovalPending')</span>
    </h5>
    <table class="table table-striped">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('camp.Name')</th>
            <th>@lang('registration.Applicants')</th>
            @role('admin')
                <th>@lang('camp.CampMakerCount')</th>
            @endrole
            <th>@lang('camp.Category')</th>
            <th>@lang('camp_procedure.CampProcedure')</th>
            <th>@lang('camp.GradingType')</th>
            @role('admin')
                <th>@lang('camp.AcceptableRegions')</th>
            @endrole
            <th>@lang('camp.Status')</th>
            <th>@lang('qualification.IsAnnounced')</th>
            <th>@lang('app.Actions')</th>
        </thead>
	    @foreach ($camps as $camp)
	    <tr>
	        <th scope="row">{{ ++$i }}</th>
	        <td class="text-truncate text-truncate-250"><a target="_blank" href="{{ route('camps.show', $camp->id) }}">{{ $camp }}</a></th>
            @php
                if ($camp->approved) {
                    $registration_count = $camp->registrations_conditional()->count();
                    if ($camp->quota)
                        $registration_count = "{$registration_count} / {$camp->quota}";
                } else
                    $registration_count = 0;
                $question_set = $camp->question_set;
            @endphp
            <td class="fit"><a target="_blank" href="{{ $question_set && $question_set->candidate_announced ? route('qualification.candidate_result', $question_set->id) : route('camps.registration', $camp->id) }}">{{ $registration_count }}</a></td>
            @role('admin')
                <td class="text-muted">{{ $camp->camp_makers()->count() }}</td>
            @endrole
            <td class="text-truncate text-truncate-150"><a target="_blank" href="{{ route('camps.by_category', $camp->camp_category_id) }}">{{ $camp->camp_category }}</a></td>
            <td class="text-muted">
                @foreach ($camp->getTags() as $glyph => $tag)
                    <label class="badge badge-secondary font-weight-normal"><i class="{{ $glyph }} mr-1 fa-xs"></i>{{ $tag }}</label>
                @endforeach
            </td>
            <td class="text-muted fit">{{ $camp->gradingType() }}</td>
            @role('admin')
                <td>
                    @foreach ($camp->getAcceptableRegions($string = false) as $region)
                        <label class="badge badge-dark font-weight-normal">{{ $region }}</label>
                    @endforeach
                </td>
            @endrole
            <td class="fit">
                <h5><span class="badge badge-{{ $camp->approved ? 'success' : 'warning' }} font-weight-normal"><i class="{{ $camp->approved ? 'fas fa-check' : 'far fa-clock' }} fa-xs mr-1"></i>{{ $camp->approved ? trans('camp.Approved') : trans('camp.ApprovalPending') }}</span></h5>
            </td>
            <td>
                @if ($question_set)
                    <h5><span class="badge badge-{{ $question_set->candidate_announced ? 'success' : 'warning' }} font-weight-normal"><i class="{{ $question_set->candidate_announced ? 'fas fa-bullhorn' : 'far fa-clock' }} fa-xs mr-1"></i>{{ $question_set->candidate_announced ? trans('qualification.Announced') : trans('camp.ApprovalPending') }}</span></h5>
                @else
                    @lang('app.N/A')
                @endif
            </td>
            <td class="fit">
                @if (!$camp->approved)
                    @can('camp-approve')
                        <a class="btn btn-success" href="{{ route('camps.approve', $camp->id) }}"><i class="fas fa-check mr-1 fa-xs"></i>@lang('app.Approve')</a>
                    @endcan
                @else
                    @can('question-edit')
                        @if ($camp->camp_procedure->candidate_required)
                            <a class="btn btn-primary" href="{{ route('questions.show', $camp->id) }}"><i class="far fa-file-alt mr-1 fa-xs"></i>@lang('camp.EditQuestions')</a>
                        @endif
                    @endcan
                @endif
                @can('camp-edit')
                    <a class="btn btn-info" href="{{ route('camps.edit', $camp->id) }}"><i class="fas fa-pencil-alt mr-1 fa-xs"></i>@lang('camp.Edit')</a>
                @endcan
                <a class="btn btn-outline-primary" href="{{ route('analytic.analytic', $camp->id) }}"><i class="fas fa-chart-bar mr-1 fa-xs"></i>@lang('camp.ViewAnalytic')</a>
                @can('camp-delete')
                    <button type="button" class="btn btn-danger" data-action="{{ route('camps.destroy', $camp->id) }}" data-toggle="modal" data-target="#modal">
                        <i class="fas fa-trash-alt mr-1 fa-xs"></i>@lang('app.Delete')
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
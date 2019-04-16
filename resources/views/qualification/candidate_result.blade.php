@extends('layouts.blank')

@section('header')
    @lang('qualification.ChosenCampers')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('custom-width')
    <div class="col-12 col-md-9">
@endsection

@php
    $camp_procedure = $camp->camp_procedure;
    $deposit_required = $camp_procedure->deposit_required;
    $interview_required = $camp_procedure->interview_required;
@endphp

@section('script')
    @if ($interview_required)
        <script src="{{ asset('js/check-unsaved.js') }}"></script>
    @endif
    <script src="{{ asset('js/status-popover.js') }}"></script>
    @include('components.qualification.status_popover')
@endsection

@section('content')
    <div class="d-flex justify-content-between">
        <h2>@lang('qualification.Candidates')</h2>
        <form class="form d-inline-flex" method="GET" action="{{ route('qualification.candidate_result', $question_set->id) }}">
            @component('components.input', [
                'input_type' => 'checkbox',
                'radio_class' => 'my-auto',
                'objects' => [
                    (object)[
                        'id' => 1,
                        'name' => trans('qualification.OnlyTruePassed'),
                    ]
                ],
                'getter' => 'name',
                'nowrapper' => 1,
                'name' => 'only_true_passed',
                'value' => $only_true_passed,
            ])
            @endcomponent
            @component('components.submit', [
                'label' => trans('app.Update'),
                'glyph' => 'fas fa-filter fa-xs ',
            ])
            @endcomponent
        </form>
    </div>
    <span class="text-muted">{{ $summary }}</span>
    <br/>
    <span class="text-muted font-weight-bold">@lang('qualification.WhoConfirmedWithin', [ 'who' => trans('qualification.Candidates'), 'date' => $camp->getConfirmationDate() ])</span>
    @if ($interview_required)
        <form id="form" method="POST" action="{{ route('qualification.interview_save', $camp->id) }}">
        @csrf
    @endif
    <table class="table table-striped">
        <thead>
            <th>@lang('app.No_')</th>
            <th>@lang('account.FullName')</th>
            <th>@lang('account.School')</th>
            <th>@lang('camper.Program')</th>
            <th>@lang('registration.Status')
                <i class="fas fa-info-circle ml-1 fa-xs" tabindex="0" data-toggle="status-popover" data-trigger="focus"></i></th>
            @if ($deposit_required)
                <th>@lang('qualification.DepositPaid')</th>
            @endif
            @if ($interview_required)
                <th>@lang('qualification.InterviewPassed')</th>
            @endif
            @if ($camp->parental_consent)
                <th>@lang('qualification.ConsentUploaded')</th>
            @endif
            <th>@lang('app.Actions')</th>
        </thead>
        @foreach ($candidates as $candidate)
            @php
                $registration = $candidate->registration;
                $camper = $candidate->camper;
                $withdrawn = $registration->withdrawn();
                $form_score = $registration->form_score;
                $approved = $registration->approved_to_confirmed() || ($form_score && $form_score->checked);
                $confirmed = $registration->confirmed();
                $interviewed = $registration->interviewed_to_confirmed();
                $rejected = $registration->rejected();
                $returned = $registration->returned;
                $paid = $deposit_required ? \App\Http\Controllers\CampApplicationController::get_payment_path($registration) : true;
                $consent = $camp->parental_consent ? \App\Http\Controllers\CampApplicationController::get_consent_path($registration) : true;
                $who = $registration->approved_by ? \App\User::find($registration->approved_by) : null;
            @endphp
            <tr
                @if ($confirmed)
                    class="table-success"
                @elseif ($withdrawn || $rejected)
                    class="table-danger"
                @endif
            >
                <th scope="row">{{ ++$i }}</th>
                <th><a href="{{ route('qualification.show_profile_detailed', $registration->id) }}">{{ $camper->getFullName() }}</a></th>
                <td class="text-truncate text-truncate-450" title="{{ $camper->school }}">{{ $camper->school }}</td>
                <td>{{ $camper->program }}</td>
                <td class="fit">
                    <div
                        @if ($registration->approved() && $who)
                            data-toggle="status" title="{{ trans('qualification.ApprovedBy', [ 'who' => $who->getFullName() ]) }}"
                        @endif
                    >
                    </div>
                </td>
                @if ($deposit_required)
                    @php $text_class = $paid ? $approved ? 'text-success' : 'text-secondary' : 'text-danger' @endphp
                    <td class="text-center {{ $text_class }}">
                        @if ($paid)
                            <a class="{{ $text_class }}"
                                href="{{ route('camp_application.payment_download', $registration->id) }}"
                                title=@lang('qualification.ViewPaymentSlip')
                            >{{ $approved ? trans('app.Yes') : trans('qualification.SlipNotYetApproved') }}<i class="fas fa-search-dollar 2x ml-2"></i></a>
                        @else
                            @lang('app.No')
                        @endif
                    </td>
                @endif
                @if ($interview_required)
                    <td class="text-center">
                        <input type="checkbox" name="{{ $registration->id }}"
                            @if ($withdrawn || $approved || $confirmed || $question_set->interview_announced)
                                disabled
                            @endif
                            @if ($interviewed)
                                checked
                            @endif
                        >
                    </td>
                @endif
                @if ($camp->parental_consent)
                    <td class="text-center{{ $consent ? ' text-success' : ' text-danger' }}">
                        @if ($consent)
                            <a class="text-success" href="{{ route('camp_application.consent_download', $registration->id) }}" title=@lang('qualification.ViewConsentForm')>
                                @lang('app.Yes')<i class="far fa-eye fa-xs ml-1"></i>
                            </a>
                        @else
                            @lang('app.No')
                        @endif
                    </td>
                @endif
                <td class="fit">
                    @role('admin')
                        @if (!$withdrawn && !$confirmed)
                            <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger btn-sm">TW</a>
                        @endif
                        @if ($paid && $consent && $approved && !$confirmed && !$withdrawn)
                            <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-success btn-sm">TC</a>
                        @endif
                    @endrole
                </td>
            </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $candidates->links() !!}
    </div>
    @if ($interview_required)
        @php $question_set = $camp->question_set @endphp
            <div class="text-center">
                @component('components.submit', [
                    'label' => trans('app.SaveChanges'),
                    'class' => 'btn btn-primary w-50',
                    'glyph' => 'far fa-save fa-xs',
                    'disabled' => !$question_set->interview_announced,
                ])
                @endcomponent
                <small class="form-text text-muted mb-2">@lang('qualification.InterviewSaveDesc')</small>
                <a class="btn btn-danger w-50{{ $question_set->interview_announced ? ' disabled' : null }}" href="{{ route('qualification.interview_announce', $question_set->id) }}"><i class="fas fa-bullhorn fa-xs mr-2"></i>@lang('qualification.AnnounceInterview')</a>
                <small class="form-text text-muted">@lang('qualification.InterviewAnnounceDesc')</small>
            </div>
        </form>
    @endif
    @if ($question_set->total_score)
        <h2>@lang('qualification.Backups')</h2>
        @if ($backups->isEmpty())
            @lang('app.None')
        @else
            <span class="text-muted">{{ $backup_summary }}</span>
            @if ($can_get_backups)
                <br/>
                <span class="text-muted font-weight-bold">@lang('qualification.WhoConfirmedWithin', [ 'who' => trans('qualification.Backups'), 'date' => $camp->getConfirmationDate($backup = true) ])</span>
            @endif
            <table class="table table-striped">
                <thead>
                    <th>@lang('app.No_')</th>
                    <th>@lang('account.FullName')</th>
                    <th>@lang('account.School')</th>
                    <th>@lang('camper.Program')</th>
                    <th>@lang('registration.Status')</th>
                    @role('admin')
                        <th>@lang('app.Actions')</th>
                    @endrole
                </thead>
                @php
                    $j = 0;
                @endphp
                @foreach ($backups as $candidate)
                    @php
                        $registration = $candidate->registration;
                        $camper = $candidate->camper;
                        $withdrawn = $registration->withdrawn();
                        $confirmed = $registration->confirmed();
                    @endphp
                    <tr
                        @if ($confirmed)
                            class="table-success"
                        @elseif ($withdrawn)
                            class="table-danger"
                        @endif
                    >
                        <th scope="row">{{ ++$j }}</th>
                        <th><a href="{{ route('qualification.show_profile_detailed', $registration->id) }}">{{ $camper->getFullName() }}</a></th>
                        <td class="text-truncate text-truncate-450" title="{{ $camper->school }}">{{ $camper->school }}</td>
                        <td>{{ $camper->program }}</td>
                        <td class="fit">{{ $registration->getStatus() }}</td>
                        <td class="fit">
                            @role('admin')
                                @if ($can_get_backups && !$withdrawn && !$confirmed)
                                    <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger">TW</a>
                                    <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-success">TC</a>
                                @endif
                            @endrole
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif
    <div class="text-center mt-4">
        <a class="btn btn-primary w-50" href="{{ route('qualification.data_download_selection', $question_set->id) }}"><i class="fa fa-download fa-xs mr-2"></i>@lang('qualification.DownloadData')</a>
    </div>
@endsection
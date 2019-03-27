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
@endsection

@section('content')
    <h2>@lang('qualification.Candidates')</h2>
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
            <th>@lang('registration.Status')</th>
            @if ($deposit_required)
                <th>@lang('qualification.DepositPaid')</th>
            @endif
            @if ($interview_required)
                <th>@lang('qualification.InterviewPassed')</th>
            @endif
            <th>@lang('app.Actions')</th>
        </thead>
        @php
            $i = 0;
        @endphp
        @foreach ($candidates as $candidate)
            @php
                $registration = $candidate->registration;
                $camper = $candidate->camper;
                $withdrawed = $registration->withdrawed();
                $approved = $registration->approved();
                $confirmed = $registration->confirmed();
                $interviewed = $registration->interviewed_to_confirmed();
                $rejected = $registration->rejected();
                $returned = $registration->returned;
                $paid = $deposit_required ? \App\Http\Controllers\CampApplicationController::get_payment_path($registration) : true;
            @endphp
            <tr
                @if ($confirmed)
                    class="table-success"
                @elseif ($withdrawed || $rejected)
                    class="table-danger"
                @elseif (!$paid || $returned)
                    class="table-warning"
                @endif
            >
                <th scope="row">{{ ++$i }}</th>
                <th><a href="{{ route('profiles.show', $camper->id) }}">{{ $camper->getFullName() }}</a></th>
                <td class="text-truncate text-truncate-450" title="{{ $camper->school }}">{{ $camper->school }}</td>
                <td>{{ $camper->program }}</td>
                <td class="fit">{{ $registration->getStatus() }}</td>
                @if ($deposit_required)
                    <td class="text-center{{ $paid ? ' text-success table-success' : ' text-danger table-danger' }}">{{ $paid ? trans('app.Yes') : trans('app.No') }}</td>
                @endif
                @if ($interview_required)
                    <td class="text-center">
                        <input type="checkbox" name="{{ $registration->id }}"
                            @if ($withdrawed || $approved || $confirmed || $question_set->interview_announced)
                                disabled
                            @endif
                            @if ($interviewed)
                                checked
                            @endif
                        >
                    </td>
                @endif
                <td class="fit">
                    <a href="{{ route('qualification.show_profile_detailed', $registration->id) }}" target="_blank" class="btn btn-secondary"><i class="far fa-eye mr-1 fa-xs"></i>@lang('qualification.ViewProfile')</a>
                    @role('admin')
                        @if (!$withdrawed && !$confirmed)
                            <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger">T Withdraw</a>
                        @endif
                        @if ($paid && $approved)
                            <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-success">T Confirm</a>
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
                    'label' => trans('app.Save'),
                    'class' => 'btn btn-primary w-25',
                    'glyph' => 'far fa-save fa-xs',
                    'disabled' => $question_set->interview_announced,
                ])
                @endcomponent
                <a class="btn btn-danger w-25{{ $question_set->interview_announced ? ' disabled' : null }}" href="{{ route('qualification.interview_announce', $question_set->id) }}"><i class="fas fa-bullhorn fa-xs mr-2"></i>@lang('qualification.AnnounceInterview')</a>
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
                    $i = 0;
                @endphp
                @foreach ($backups as $candidate)
                    @php
                        $registration = $candidate->registration;
                        $camper = $candidate->camper;
                        $withdrawed = $registration->withdrawed();
                        $confirmed = $registration->confirmed();
                    @endphp
                    <tr
                        @if ($confirmed)
                            class="table-success"
                        @elseif ($withdrawed)
                            class="table-danger"
                        @endif
                    >
                        <th scope="row">{{ ++$i }}</th>
                        <th><a href="{{ route('profiles.show', $camper->id) }}">{{ $camper->getFullName() }}</a></th>
                        <td class="text-truncate text-truncate-450" title="{{ $camper->school }}">{{ $camper->school }}</td>
                        <td>{{ $camper->program }}</td>
                        <td class="fit">{{ $registration->getStatus() }}</td>
                        <td class="fit">
                            <a href="{{ route('qualification.show_profile_detailed', $registration->id) }}" target="_blank" class="btn btn-secondary"><i class="far fa-eye mr-1 fa-xs"></i>@lang('qualification.ViewProfile')</a>
                            @role('admin')
                                @if ($can_get_backups && !$withdrawed && !$confirmed)
                                    <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger">T Withdraw</a>
                                    <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-success">T Confirm</a>
                                @endif
                            @endrole
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif
    @endif
    <div class="text-center mt-4">
        <a target="_blank" class="btn btn-primary w-50" href="{{ route('qualification.data_download_selection', $question_set->id) }}">@lang('qualification.DownloadData')</a>
    </div>
@endsection
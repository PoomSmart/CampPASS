@extends('layouts.blank')

@section('header')
    @lang('qualification.PassedCampers')
@endsection

@section('subheader')
    {{ $camp }}
@endsection

@section('custom-width')
    <div class="col-12 col-md-9">
@endsection

@section('content')
    <h2>@lang('qualification.Candidates')</h2>
    <div class="d-flex">
        <span class="text-muted">{{ $summary }}</span>
    </div>
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
        @foreach ($form_scores as $form_score)
            @php
                $registration = $form_score->registration;
                $camper = $registration->camper;
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
                <td class="text-truncate">{{ $camper->school }}</td>
                <td>{{ $camper->program }}</td>
                <td class="fit">{{ $registration->getStatus() }}</td>
                @role('admin')
                    <td class="fit">
                        @if (!$withdrawed)
                            <a href="{{ route('camp_application.withdraw', $registration->id) }}" class="btn btn-danger">T Withdraw</a>
                        @endif
                        @if (!$confirmed)
                            <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-success">T Confirm</a>
                        @endif
                    </td>
                @endrole
            </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {!! $form_scores->links() !!}
    </div>
    @if ($camp->question_set->total_score)
        <h2>@lang('qualification.Backups')</h2>
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
            @foreach ($backups as $form_score)
                @php
                    $registration = $form_score->registration;
                    $camper = $registration->camper;
                    $confirmed = $registration->confirmed();
                @endphp
                <tr
                    @if ($confirmed)
                        class="table-success"
                    @endif
                >
                    <th scope="row">{{ ++$i }}</th>
                    <th><a href="{{ route('profiles.show', $camper->id) }}">{{ $camper->getFullName() }}</a></th>
                    <td class="text-truncate">{{ $camper->school }}</td>
                    <td>{{ $camper->program }}</td>
                    <td class="fit">{{ $registration->getStatus() }}</td>
                    @role('admin')
                        <td class="fit">
                            @if (!$confirmed)
                                <a href="{{ route('camp_application.confirm', $registration->id) }}" class="btn btn-success">T Confirm</a>
                            @endif
                        </td>
                    @endrole
                </tr>
            @endforeach
        </table>
    @endif
@endsection
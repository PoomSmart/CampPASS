@extends('layouts.table')

@section('header')
    {{ $camp->getName() }}
@endsection

@section('content')
    <p>{{ $category }} - {{ $camp->camp_procedure()->getTitle() }} - {{ $camp->getShortDescription() }}</p>
    @can('camper-list')
        @can('answer-list')
            <?php $rankable = $camp->camp_procedure()->candidate_required && !is_null($camp->question_set()); ?>
        @endcan
        <div class="row">
            <div class="col-12">
                @if (count($data))
                    <h3>Registered Campers</h3>
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ trans('registration.ID') }}</th>
                            <th>{{ trans('account.ID') }}</th>
                            <th>{{ trans('app.LocalizedName') }}</th>
                            <th>{{ trans('account.School') }}</th>
                            <th>{{ trans('account.Program') }}</th>
                            <th>{{ trans('account.Status') }}</th>
                            <th>{{ trans('app.Actions') }}</th>
                        </tr>
                        @foreach ($data as $key => $registration)
                            <?php $camper = $registration->camper(); ?>
                            <tr>
                                <td>{{ $registration->id }}</td>
                                <td>{{ $camper->id }}</td>
                                <td>{{ $camper->getFullName() }}</td>
                                <td>{{ $camper->school()->getName() }}</td>
                                <td>{{ $camper->program()->getName() }}</td>
                                <td>{{ $registration->getStatus() }}</td>
                                <td>
                                    @if ($rankable)
                                        <a class="btn btn-info" href="{{ route('qualification.answer_view', [$registration->id, $camp->question_set()->id]) }}">{{ trans('registration.View') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {!! $data->links() !!}
                @else
                    <p>{{ trans('registration.EmptyRegistration') }}</p>
                @endif
            </div>
            @if ($rankable && count($data))
                <div class="col-12">
                    <a class="btn btn-warning" href="{{ route('qualification.candidate_rank', $camp->question_set()->id) }}">{{ trans('qualification.Rank') }}</a>
                </div>
            @endif
        </div>
    @endcan
    @role('camper')
        <div class="row">
            <div class="col-12">
                <?php
                    $apply_text = null;
                    $camper = \Auth::user();
                    $ineligible_reason = $camper->getIneligibleReasonForCamp($camp);
                    $disabled = false;
                    if ($ineligible_reason)
                        $disabled = true;
                    $registration = $camper->registrationForCamp($camp);
                    $status = $registration ? $registration->status : -1;
                    $camp_procedure = $camp->camp_procedure();
                ?>
                @switch ($status)
                    @case (\App\Enums\RegistrationStatus::DRAFT)
                    @case (\App\Enums\RegistrationStatus::RETURNED)
                        <?php $apply_text = $camp_procedure->candidate_required ? trans('app.Edit') : null; ?>
                        @break
                    @case (\App\Enums\RegistrationStatus::APPLIED)
                        <?php $apply_text = trans('registration.APPLIED'); ?>
                        @break
                    @case (\App\Enums\RegistrationStatus::APPROVED)
                        <?php $apply_text = trans('registration.APPROVED'); ?>
                        @break
                    @case (\App\Enums\RegistrationStatus::QUALIFIED)
                        <?php $apply_text = trans('registration.QUALIFIED'); ?>
                        @break
                @endswitch
                <?php if (!$apply_text) $apply_text = trans('registration.Apply'); ?>
                <a class="btn btn-primary{{ $disabled || $status >= \App\Enums\RegistrationStatus::APPLIED ? ' disabled' : ''}}"
                    href="{{ route('camp_application.landing', $camp->id) }}"
                >{{ $apply_text }}</a>
                <a class="btn btn-secondary" target="_blank" href="{{ $camp->getURL() }}">{{ trans('camp.ContactCampMaker') }}</a>
            </div>
        </div>
        @if ($ineligible_reason)
        <div class="row mt-2">
            <div class="col-12">
                <p class="text-danger">{{ $ineligible_reason }}</p>
            </div>
        </div>
        @endif
    @endrole
@endsection
@extends('layouts.table')

@section('header')
    {{ $camp }}
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
                            <th>@lang('registration.ID')</th>
                            <th>@lang('account.ID')</th>
                            <th>@lang('app.LocalizedName')</th>
                            <th>@lang('account.School')</th>
                            <th>@lang('camper.Program')</th>
                            <th>@lang('account.Status')</th>
                            <th>@lang('app.Actions')</th>
                        </tr>
                        @foreach ($data as $key => $registration)
                            <?php $camper = $registration->camper(); ?>
                            <tr>
                                <td>{{ $registration->id }}</td>
                                <td>{{ $camper->id }}</td>
                                <td>{{ $camper->getFullName() }}</td>
                                <td>{{ $camper->school() }}</td>
                                <td>{{ $camper->program() }}</td>
                                <td>{{ $registration->getStatus() }}</td>
                                <td>
                                    @if ($rankable)
                                        <a class="btn btn-info" href="{{ route('qualification.answer_grade', [$registration->id, $camp->question_set()->id]) }}">@lang('registration.View')</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {!! $data->links() !!}
                @else
                    <p>@lang('registration.EmptyRegistration')</p>
                @endif
            </div>
            @if ($rankable && count($data))
                <div class="col-12">
                    <a class="btn btn-warning" href="{{ route('qualification.candidate_rank', $camp->question_set()->id) }}">@lang('qualification.Rank')</a>
                </div>
            @endif
        </div>
    @endcan
    @role('camper')
        <div class="row">
            <div class="col-12">
                <?php
                    $info = \App\Http\Controllers\CampApplicationController::getApplyButtonInformation($camp);
                    $apply_text = $info['text'];
                    $disabled = $info['disabled'];
                ?>
                <a class="btn btn-primary{{ $disabled ? ' disabled' : ''}}"
                    href="{{ route('camp_application.landing', $camp->id) }}"
                >{{ $apply_text }}</a>
                <a class="btn btn-secondary" target="_blank" href="{{ $camp->getURL() }}">@lang('camp.ContactCampMaker')</a>
            </div>
        </div>
    @endrole
@endsection
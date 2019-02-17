@extends('layouts.card')

@section('header')
    {{ $camp }}
@endsection

@section('card_content')
    <p class="text-center">{{ $category->getName() }}</p>
    @can('answer-list')
        @php $rankable = $camp->camp_procedure()->candidate_required && !is_null($camp->question_set()); @endphp
    @endcan
    <div class="row">
        @php
            $manual_grading_required = $camp->question_set() && $camp->question_set()->manual_required && !$camp->question_set()->announced;
        @endphp
        @if ($manual_grading_required)
            <div class="col-12 text-center">
                <b class="text-info">** Manual Grading required. **</b>
            </div>
        @endif
        <div class="col-12">
            @if ($data && count($data))
                <h3>@lang('camper.RegisteredCampers')</h3>
                <table class="table table-striped">
                    <thead>
                        <th class="align-middle">@lang('registration.ID')</th>
                        <th class="align-middle">@lang('account.Name')</th>
                        <th class="align-middle">@lang('account.School')</th>
                        <th class="align-middle">@lang('camper.Program')</th>
                        <th class="align-middle">@lang('registration.Status')</th>
                        <th class="align-middle">@lang('qualification.Finalized')</th>
                        <th class="align-middle">@lang('app.Actions')</th>
                    </thead>
                    @foreach ($data as $key => $form_score)
                        @php
                            $registration = $form_score->registration();
                            $camper = $registration->camper();
                        @endphp
                        <tr>
                            <th class="align-middle" scope="row">{{ $registration->id }}</th>
                            <th class="align-middle"><a href="{{ route('profiles.show', $camper) }}" target="_blank">{{ $camper->getFullName() }}</a></th>
                            <td class="align-middle">{{ $camper->school() }}</td>
                            <td class="align-middle">{{ $camper->program() }}</td>
                            <td class="align-middle text-center">{{ $registration->getStatus() }}</td>
                            @php $not_finalized = $manual_grading_required && !$form_score->finalized; @endphp
                            <td class="align-middle text-center{{ $not_finalized ? ' text-danger table-danger' : ' text-success table-success' }}">{{ $not_finalized ? trans('app.No') : trans('app.Yes')  }}</td>
                            <td class="align-middle">
                                @if ($rankable)
                                    <a class="btn btn-info{{ ($registration->unsubmitted() && !\Auth::user()->isAdmin()) ? ' disabled' : '' }}"
                                        href="{{ route('qualification.answer_grade', [
                                            'registration_id' => $registration->id,
                                            'question_set_id' => $camp->question_set()->id
                                        ]) }}">@lang('registration.View')</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="d-flex justify-content-center">
                    {!! $data->links() !!}
                </div>
            @else
                <span class="text-muted">@lang('registration.EmptyRegistration')</span>
            @endif
        </div>
    </div>
@endsection

@if (isset($rankable) && $rankable && count($data))
    @section('extra-buttons')
        <a class="btn btn-warning w-50" href="{{ route('qualification.candidate_rank', $camp->question_set()->id) }}">@lang('qualification.Rank')</a>
    @endsection
@endif
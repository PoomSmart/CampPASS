@extends('layouts.table')

@section('header')
    {{ $camp->getName() }}
@endsection

@section('button')
    <!-- TODO: This will raise a permission error to guests -->
    <a class="btn btn-primary" href="{{ route('camps.index') }}">{{ trans('app.Back') }}</a>
@endsection

@section('content')
    <p>{{ $category }} - {{ $camp->camp_procedure()->getTitle() }} - {{ $camp->getShortDescription() }}</p>
    @can('camper-list')
        <div class="row">
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
                            @can('answer-list')
                                @if ($camp->camp_procedure()->candidate_required && !is_null($camp->question_set()))
                                    <a class="btn btn-info" href="{{ route('qualification.answer_view', [$registration->id, $camp->question_set()->id]) }}">{{ trans('registration.View') }}</a>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endcan
    @role('camper')
        <!-- TODO: add already-applied state -->
        <a class="btn btn-primary" href="{{ route('camp_application.landing', $camp->id) }}">{{ trans('Apply') }}</a>
        <a class="btn btn-secondary" target="_blank" href="{{ $camp->getURL() }}">{{ trans('ContactCampMaker') }}</a>
    @endrole
@endsection
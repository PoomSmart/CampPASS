@extends('layouts.table')

@section('header')
    @lang('qualification.CandidateRanking')
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>@lang('app.No_')</th>
            <th>@lang('account.FullName')</th>
            <th>@lang('qualification.Score')</th>
            <th>@lang('qualification.Passed')</th>
        </tr>
        <?php
            $i = $passed = 0;
        ?>
        @foreach ($campers as $camper)
            <tr>
                <td>{{ ++$i }}</td>
                <td><a href="{{ route('profiles.show', $camper) }}">{{ $camper->getFullName() }}</a></td>
                <td>{{ $score = $scores[$camper->id] }} / {{ $question_set->total_score }}</td>
                <td>{{ ($camper_pass = $score / $question_set->total_score >= $question_set->score_threshold) ? trans('app.Yes') : trans('app.No') }}</td>
                <?php if ($camper_pass) ++$passed; ?>
            </tr>
        @endforeach
    </table>
    <a class="btn btn-danger{{ !$passed ? ' disabled' : '' }}" href="">Announce</a>
@endsection
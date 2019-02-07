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
        <?php $i = 0; ?>
        @foreach ($campers as $camper)
            <tr>
                <td>{{ ++$i }}</td>
                <td><a href="{{ route('profiles.show', $camper) }}">{{ $camper->getFullName() }}</a></td>
                <td>{{ $score = $scores[$camper->id] }} / {{ $question_set->total_score }}</td>
                <td>{{ ($score / $question_set->total_score >= $question_set->score_threshold) ? trans('app.Yes') : trans('app.No') }}</td>
            </tr>
        @endforeach
    </table>
    <a class="btn btn-danger" href="">Announce</a>
@endsection
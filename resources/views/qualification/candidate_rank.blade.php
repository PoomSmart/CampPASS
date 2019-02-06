@extends('layouts.table')

@section('header')
    @lang('CandidateRanking')
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>@lang('app.No_')</th>
            <th>@lang('account.FullName')</th>
            <th>@lang('Score')</th>
        </tr>
        <?php $i = 0; ?>
        @foreach ($campers as $camper)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $camper->getFullName() }}</td>
                <td>{{ $scores[$camper->id] }}</td>
            </tr>
        @endforeach
    </table>
@endsection
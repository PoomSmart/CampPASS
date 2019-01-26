@extends('layouts.table')

@section('header')
    {{ trans('CandidateRanking') }}
@endsection

@section('content')
    <table class="table table-bordered">
        <tr>
            <th>{{ trans('app.No_') }}</th>
            <th>{{ trans('account.FullName') }}</th>
            <th>{{ trans('Score') }}</th>
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
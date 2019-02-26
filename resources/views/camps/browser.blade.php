@extends('layouts.blank')

@section('header')
    @lang('camp.ViewAllCamps')
@endsection

@section('content')
    @foreach ($categorized_camps as $category => $camps)
        <div class="container mt-4">
            <h3 class="mb-4"><a href="{{ route('camps.by_category', $category_ids[$category]) }}">{{ $category }}</a></h3>
            @component('components.card_columns', [
                'objects' => $camps,
                'component' => 'components.camp_block',
            ])
            @endcomponent
        </div>
    @endforeach
@endsection
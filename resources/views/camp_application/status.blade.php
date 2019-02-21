@extends('layouts.card')

@section('header')
    Application Status
@endsection

@section('card_content')
    <div class="row mt-4">
        <div class="col-md-4">
            Application Placeholder
        </div>
        <div class="col-md-8">
            <h4 class="mb-4">Application</h4>
            <p>Your application has been accepted.</p>
        </div>

        <div class="col-md-4">
            Interview Placeholder
        </div>
        <div class="col-md-8">
            <h4 class="mb-4">Interview</h4>
            <p>Please do acknowledge your attendance by clickinig confirm.</p>
            <a href="" class="btn btn-primary w-100">Confirm</a>
        </div>

        <div class="col-md-4">
            Deposit Placeholder
        </div>
        <div class="col-md-8">
            <h4 class="mb-4">Deposit</h4>
            <p>Please upload your payment slip.</p>
            <a href="" class="btn btn-primary w-100">Upload Payment Slip</a>
        </div>
        
        <div class="col-md-4">
            Qualified Placeholder
        </div>
        <div class="col-md-8">
            <h4 class="mb-4">Qualified</h4>
            <p>Congratulations! Please do acknowledge your attendance by clickinig confirm.</p>
            <a href="" class="btn btn-primary w-100">Confirm</a>
        </div>
    </div>
@endsection
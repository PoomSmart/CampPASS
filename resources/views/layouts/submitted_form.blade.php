<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="http://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link href="http://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
    </style>
</head>
<body>
    <h1>{{ $user->getFullName('en') }} ({{ $user->getFullName('th') }})</h1>
    <div class="row">
        <div class="col-3"><b>Nickname:</b></div>
        <div class="col-9">{{ $user->nickname_en }} ({{ $user->nickname_th }})</div>
        <div class="col-3"><b>Date of Birth:</b></div>
        <div class="col-9">{{ $user->dob }}</div>
        <div class="col-3"><b>Gender:</b></div>
        <div class="col-9">{{ $user->gender }}</div>
        <div class="col-3"><b>Blood Group:</b></div>
        <div class="col-9">{{ $user->blood_group }}</div>
        <div class="col-3"><b>Region:</b></div>
        <div class="col-9">{{ $user->religion_id }}</div>
    </div>
    <h3>@lang('account.Education')</h3>
    <div class="row">
        <div class="col-3"><b>School:</b></div>
        <div class="col-9">{{ $user->school_id }}</div>
        <div class="col-3"><b>Program of Study:</b></div>
        <div class="col-9">{{ $user->programs }}</div>
        <div class="col-3"><b>Education Level:</b></div>
        <div class="col-9">{{ $user->education_level }}</div>
        <div class="col-3"><b>Cumulative GPA:</b></div>
        <div class="col-9">{{ $user->cgpa }}</div>
    </div>
    <h3>@lang('profile.ContactInformation')</h3>
    <div class="row">
        <div class="col-3"><b>Mobile No.:</b></div>
        <div class="col-9">{{ $user->school_id }}</div>
        <div class="col-3"><b>Street Address:</b></div>
        <div class="col-9">{{ $user->street_address }}</div>
        <div class="col-3"><b>Province:</b></div>
        <div class="col-9">{{ $user->province }}</div>
        <div class="col-3"><b>Zip Code:</b></div>
        <div class="col-9">{{ $user->zipcode }}</div>
    </div>
    <h3>@lang('profile.EmergencyContactInformation')</h3>
    <div class="row">
        <div class="col-3"><b>Guardian:</b></div>
        <div class="col-9">{{ $user->guardian_name }} {{ $user->guardian_surname }}</div>
        <div class="col-3"><b>Mobile No.:</b></div>
        <div class="col-9">{{ $user->guardian_mobile_no }}</div>
        <div class="col-3"><b>Role:</b></div>
        <div class="col-9">{{ $user->guardian_role }}</div>
    </div>
    <h3>@lang('app.CampApplicationForm')</h3>
    <div class="row">
        
    </div>
</body>
</html>
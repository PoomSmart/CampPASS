<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="http://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link href="http://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: dbsathornnow;
            src: url('/fonts/DBSathornNowBold.ttf');
            font-weight: 700;
        }

        @font-face {
            font-family: dbsathornnow;
            src: url('/fonts/DBSathornNowMed.ttf');
            font-weight: 600;
        }

        @font-face {
            font-family: dbsathornnow;
            src: url('/fonts/DBSathornNow.ttf');
            font-weight: normal;
        }
       
        body {
            font-family: dbsathornnow;
        }
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
        <div class="col-9">
            @php
            $genders = [ 
                trans('account.Male'),
                trans('account.Female'),
                trans('account.OtherGender'),
        ]
            @endphp
            {{ $genders[$user->gender] }} 
        </div>
        <div class="col-3"><b>Blood Group:</b></div>
        <div class="col-9">
            @php
            $blood_groups = ['A','O','B','AB']
            @endphp
            {{ $blood_groups[$user->blood_group] }}
        </div>
        <div class="col-3"><b>Region:</b></div>
        <div class="col-9">{{ $user->religion }}</div>
    </div>
    <h3>@lang('account.Education')</h3>
    <div class="row">
        <div class="col-3"><b>School:</b></div>
        <div class="col-9">{{ $user->school }}</div>
        <div class="col-3"><b>Program of Study:</b></div>
        <div class="col-9">{{ $user->program }}</div>
        <div class="col-3"><b>Education Level:</b></div>
        <div class="col-9">
            @php
            $education_levels = [
                'P',
                'S',
                'M1',
                'M2',
                'M3',
                'M4',
                'M5',
                'M6',
                'U',
            ]    
            @endphp
            {{ trans('year.'.$education_levels[$user->education_level]) }}
        </div>
        <div class="col-3"><b>Cumulative GPA:</b></div>
        <div class="col-9">{{ $user->cgpa }}</div>
    </div>
    <h3>@lang('profile.ContactInformation')</h3>
    <div class="row">
        <div class="col-3"><b>Mobile No.:</b></div>
        <div class="col-9">{{ $user->mobile_no }}</div>
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
        <div class="col-9">
            {{ [trans('account.Father'),trans('account.Mother'),trans('app.Other')][$user->guardian_role] }}</div>
    </div>
    <h3>@lang('app.CampApplicationForm')</h3>
    <div class="row">
            <div class="col-12"><b>Question</b></div>
            <div class="col-12"><b>Answer</b></div>
        
    </div>
</body>
</html>
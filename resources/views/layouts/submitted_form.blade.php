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
    </div>
</body>
</html>
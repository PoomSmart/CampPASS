<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="{{ asset('js/app.js') }}"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: "Segoe UI";
            font-size: 120%;
        }
        .page {
            overflow: hidden;
            page-break-after: always;
        }
        .page-last {
            overflow: hidden;
        }
    </style>
</head>
<body>
    @yield('body')
</body>
</html>
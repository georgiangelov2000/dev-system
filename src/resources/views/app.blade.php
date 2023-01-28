<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link type="text/css" href="{{ asset('css/adminlte.min.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ asset('css/all.min.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{mix('css/app.css')}}" rel="stylesheet" />
    </head>

    <body class="sidebar-mini layout-fixed" style="height: auto;">
        <div class='wrapper'>
            @include("layouts.header")

            <div class="container">
                @yield('content')
            </div>
        </div>
    </body>

    <script type="text/javascript" href="{{ asset('js/jquery.min.js') }}"></script>
    <script type="text/javascript" href="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" href="{{ asset('js/adminlte.min.js') }}"></script>
    <script type="text/javascript" href="{{ asset('js/jqueryui.min.js') }}"></script>

</html>

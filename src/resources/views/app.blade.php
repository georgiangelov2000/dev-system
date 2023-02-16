<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link type="text/css" href="{{ asset('css/adminlte.min.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ mix('css/all.min.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ mix('css/toastr.min.css') }}" rel="stylesheet"/>
        <link type="text/css" href="{{ mix('css/template.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ mix('css/sweetalert2.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ mix('css/datatables.min.css') }}" rel="stylesheet" />
    </head>
    <!--"sidebar-mini layout-fixed"-->
    <body class="sidebar-mini layout-fixed {{$isAuth ? "" : "body-class"}}" style="height: auto;">
        <div class='wrapper'>
            @include("layouts.header")
            @auth
            <div class='container'>
                @yield('content')
            </div>
            @else
                @include('layouts.auth')
            @endauth
        </div>

        <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/jqueryui.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/bootstrap.bundle.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/adminlte.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/toastr.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/sweetalert2.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/datatables.min.js') }}"></script>

        <script type="text/javascript">
            $(document).ready(function() {                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            })
        </script>
        
    @stack('scripts')
        

    </body>

</html>

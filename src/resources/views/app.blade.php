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
        <link type="text/css" href="{{ mix('css/bootstrap-select.min.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ mix('css/datepicker.min.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ mix('css/daterangepicker.min.css') }}" rel="stylesheet" />
        <link type="text/css" href="{{ mix('css/icheck-bootstrap.min.css') }}" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        
        <link rel="icon" type="image/png" href="/storage/images/static/logo.jpg">
    </head>
    <!--"sidebar-mini layout-fixed"-->
    <body class="sidebar-mini layout-fixed sidebar-collapse {{$isAuth ? "" : "body-class"}}" style="height: auto;">
        <div class='wrapper'>
            @include("layouts.header")
            @auth
            <div class="content-wrapper">
                <section class="content">
                    <div class='container-fluid'>
                        <div class="col-12">
                            @yield('content')
                        </div>
                    </div>
                </section>
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
        <script type="text/javascript" src="{{ mix('js/bootstrap-select-min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/datepicker.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/moment.min.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/daterangepicker.min.js') }}"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
        
        <script>
            @if (Session::has('message'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true
                }
                toastr.success("{{ session('message') }}");
            @endif
            @if (Session::has('success'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true
                }
                toastr.success("{{ session('success') }}");
            @endif
            @if (Session::has('error'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true
                }
                toastr.error("{{ session('error') }}");
            @endif
            @if (Session::has('info'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true
                }
                toastr.info("{{ session('info') }}");
            @endif
            @if (Session::has('warning'))
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true
                }
                toastr.warning("{{ session('warning') }}");
            @endif
        </script>

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

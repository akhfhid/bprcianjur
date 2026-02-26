<?php
header('Access-Control-Allow-Origin: localhost');
?>
<!DOCTYPE html>
<!--[if IE 9]><html class ="ie9 no-js" lang="en"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIKAP @yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('polished/polished.min.css') }}">
    <link rel="stylesheet" href="{{ asset('polished/iconic/css/open-iconic-bootstrap.min.css') }}">
    <!-- 	<link rel="stylesheet" href="{{ asset('summernote/summernote-bs4.css') }}">
 <link rel="stylesheet" href="{{ asset('summernote/summernote-bs4.js') }}"> -->
<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css"> -->
    <!--<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css"> -->
    <!-- General CSS Files -->


    <!-- CSS Libraries -->


    <link rel="stylesheet" href="{{ asset('DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('DataTables/Buttons-1.5.6/css/buttons.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('owlcarousel/dist/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('owlcarousel/dist/assets/owl.theme.default.min.css') }}">
    <!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css"
        integrity="sha256-pODNVtK3uOhL8FUNWWvFQK0QoQoV3YA9wGGng6mbZ0E=" crossorigin="anonymous" />
    <!-- Template CSS -->

    <style>
        .grid-highlight {
            padding-top: 1rem;
            padding-bottom: 1rem;
            background-color: #5c6ac4;
            border: 1px solid #202e78;
            color: #fff;
        }

        hr {
            margin: 6rem 0;
        }

        hr+.display-3,
        hr+.display-2+.display-3 {
            margin-bottom: 2rem;
        }
    </style>
    <!--<script type="text/javascript">
        document.documentElement.classname = document.documentElement.calssName.replace('no-js', 'js') +
            (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1") ? 'svg' :
                'no-svg');
    </script>-->
</head>

<body>
    <nav class="navbar navbar-expand p-0">
        <a class="navbar-brand text-center col-xs-12 col-md-3 col-lg-2 mr-0" href="/home"> SIKAP</a>
        <button class="btn btn-link d-block d-md-none" data-toggle="collapse" data-target="#sidebar-nav"
            role="button"><span class="oi oi-menu"></span>
        </button>
        <input class="border-dark bg-primary-darkest form-control d-none d-md-block w-50 ml-3 mr-2" type="text"
            placeholder="Search" aria-label="Search">
        <div class="dropdown d-none d-md-block">
            @if (\Auth::user())
                <button class="btn btn-link btn-link-primary dropdown-toggle" id="navbar-dropdown"
                    data-toggle="dropdown">
                    {{ Auth::user()->name }}
                </button>

                <div class="dropdown-menu dropdown-menu-right" id="navbar-dropdown">
                    <a href="#" class="dropdown-item">Profile</a>
                    <a href="{{ route('reset') }}" class="dropdown-item">Setting</a>
            @endif
            <div class="dropdown-divider"></div>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="dropdown-item" style="cursor:ponter">Sign Out</button>
                </form>
            </li>
        </div>
        </div>
    </nav>

    <div class="container-fluid h-00 p-0">
        <div style="min-height: 100%" class="flex-row d-flex align-items-stretch m-0">
            <div class="polished-sidebar bg-light col-12 col-md-3 col-lg-2 p-0 collapse d-md-inline" id="sidebar-nav">
                <ul class="polished-sidebar-menu ml-0 pt-4 p-0 d-md-block">
                    <input class="border-dark form-control d-block d-md-none mb-4" type="text" placeholder="Search"
                        aria-label="Search" />
                    @if (Auth::check())
                        @if (auth()->user()->roles == 'ADMIN')
                            <x-sidebar />
                        @elseif (auth()->user()->roles == 'SUPERVISOR')
                            <x-sidebar-supervisor />
                        @elseif (auth()->user()->roles == 'USER')
                            <x-sidebar-user />
                        @elseif (auth()->user()->roles == 'PINCAB')
                            <x-sidebar-pincab />
                        @elseif (auth()->user()->roles == 'KADIV')
                            <x-sidebar-kadiv />
                        @elseif (auth()->user()->roles == 'PATUH')
                            <x-sidebar-kepatuhan />
                        @elseif (auth()->user()->roles == 'DIRUT')
                            <x-sidebar-direksi />
                        @elseif (auth()->user()->roles == 'DIRBIS')
                            <x-sidebar-dirbis />
                        @elseif (auth()->user()->roles == 'STAFF_SDM')
                            <x-sidebar-staff-s-d-m />
                        @elseif (auth()->user()->roles == 'ADMIN_SDM')
                            <x-sidebar-s-d-m />
                        @endif
                    @endif

                    <div class="d-block d-md-none">
                        <div class="dropdown-divider"></div>
                        <li><a href="">Profile</a></li>
                        <li><a href="#">Setting</a></li>
                        <li>
                            <Form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="dropdown-item" style="cursor:pointer">Sign Out</button>
                            </Form>
                        </li>
                    </div>
                </ul>
                <div class="pl-3 d-none d-md-block position-fixed" style="bottom: 0px">
                    Copyright &copy; BPR Cianjur@2020
                </div>
            </div>
            <div class="col-lg-10 col-md-9 p-4">
                <div class="row">
                    <div class="col-md-12 pl-3 pt-2">
                        <div class="pl-3">
                            <h3>@yield('pageTitle')</h3>
                            <br>
                        </div>
                    </div>
                </div>

                @yield('content')

            </div>
        </div>
        <script language="JavaScript">
            // Disable Klik Kanan
            document.addEventListener("contextmenu", function(e) {
                e.preventDefault();
                { //Alt+c, Alt+v will also be disabled sadly.
                    alert('Ooops Tidak Bisa');
                }
            }, false);

            // Disable CTRL+U
            //document.onkeydown = function (e) {
            //  if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85 || e.keyCode ===
            // 117)) { //Alt+c, Alt+v will also be disabled sadly.
            //alert('Ooops Tidak Bisa');
            //}
            //return false;
            //};
        </script>
        <!--<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>

    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"
        integrity="sha256-sPB0F50YUDK0otDnsfNHawYmA5M0pjjUf4TvRJkGFrI=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.js"
        integrity="sha256-siqh9650JHbYFKyZeTEAhq+3jvkFCG8Iz+MHdr9eKrw=" crossorigin="anonymous"></script>-->
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

        <!-- Datatables -->
        <script src="{{ asset('DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js') }}"></script>

        <script src="{{ asset('DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('DataTables/Buttons-1.5.6/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('DataTables/JSZip-2.5.0/jszip.min.js') }}"></script>

        <script src="{{ asset('DataTables/pdfmake-0.1.36/vfs_fonts.js') }}"></script>
        <script src="{{ asset('DataTables/Buttons-1.5.6/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('DataTables/Buttons-1.5.6/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('DataTables/Buttons-1.5.6/js/buttons.colVis.min.js') }}"></script>


        @yield('footer-scripts')

</body>

</html>

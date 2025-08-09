
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Distribox</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="{{asset('front/logoEmaster.png')}}"/>
    <script src="{{asset('front/js/moment.js')}}"></script>
    <script src="{{asset('front/vendor/chart.js/Chart.js')}}"></script>

    <!-- Custom fonts for this template-->
    <link href="{{asset('front/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">

    <link href="{{asset('front/css/perso.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('front/datatables/dataTables.bootstrap4.min.css')}}">
    <!-- Custom styles for this template-->
    <link href="{{asset('front/css/sb-admin-2.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}" />


</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" style="background-color:white" href="">
            <div class="mx-3">
                <img src="{{ asset('front/logoEmaster.png') }}" alt="Logo eMaster" style="width: 50%; height: auto;">
            </div>
        </a>

        <li class="nav-item {{ Request::is('page/dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('dashboard')}}">
                <i class="fas fa-home"></i>
                <span class="text-capitalize">Tableau de bord</span>
            </a>
        </li>
        @if (auth()->user()->groupe == 'SA')
            <li class="nav-item {{ Request::is('page/entrepots/*') ? 'active' : '' }}">
                <a class="nav-link" href="{{route('entrepots.index')}}">
                    <i class="fas fa-warehouse"></i>
                    <span class="text-capitalize">Gestion des entrepots</span>
                </a>
            </li>

        @endif

        <li class="nav-item {{ Request::is('page/utilisateurs/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('users.index')}}">
                <i class="fas fa-users-cog"></i>
                <span class="text-capitalize">Gestion des utilisateurs</span>
            </a>
        </li>


        @if (auth()->user()->groupe != 'GE')

        <li class="nav-item {{ Request::is('page/produits/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('produits.index')}}">
                <i class="fas fa-store"></i>
                <span class="text-capitalize">Gestion des produits</span>
            </a>
        </li>

        @endif


        <li class="nav-item {{ Request::is('page/stock/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('stock.index')}}">
                <i class="fas fa-box"></i>
                <span class="text-capitalize">Gestion du stock</span>
            </a>
        </li>


        <li class="nav-item {{ Request::is('page/attribution/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('stock.attribution')}}">
                <i class="fas fa-exchange-alt"></i>
                <span class="text-capitalize">Attribution du stock</span>
            </a>
        </li>



        <li class="nav-item {{ Request::is('page/recouvrements/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('recouvrements.index')}}">
                <i class="fas fa-hand-holding-usd"></i>
                <span class="text-capitalize">Recouvrement</span>
            </a>
        </li>




        <li class="nav-item {{ Request::is('page/caisses/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('caisses.index')}}">
                <i class="fas fa-coins"></i>
                <span class="text-capitalize">Tenue de caisse</span>
            </a>
        </li>


        <li class="nav-item {{ Request::is('page/comptes/*') ? 'active' : '' }}">
            <a class="nav-link" href="{{route('compte.index')}}">
                <i class="fas fa-balance-scale"></i>
                <span class="text-capitalize">Compte</span>
            </a>
        </li>




        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>



    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav d-flex align-items-center">
                    <li class="nav-item  no-arrow">
                        <a class="nav-link" href="#">
                            <span class="mr-2 font-weight-bold text-uppercase btn btn-primary rounded-0">BIENVENUE {{ucfirst(Auth()->user()->personnel)}} //
                                @switch(Auth()->user()->groupe)
                                    @case('SC') Commercial @break
                                    @case('SP') Superviseur @break
                                    @case('SA') Super Admin @break
                                    @case('FO') Fournisseur @break
                                    @case('CL') Client @break
                                    @case('GE') Gerant @break
                                    @case('DM') Client DMG @break
                            @endswitch
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - User Information -->
                    <li class="nav-item  no-arrow">
                        <a class="nav-link" href="{{route('admin_pass')}}">
                            <span class="mr-2  font-weight-bold  btn btn-primary rounded-0"> <span class="d-none d-lg-inline"></span> Modifier mot de passe</span>
                        </a>
                    </li>
                    <li class="nav-item  no-arrow">
                        <a class="nav-link" href="{{route('admin_logout')}}">
                            <span class="mr-2  font-weight-bold  btn btn-danger rounded-0"> <span class="d-none d-lg-inline"></span> Déconnexion</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- placer le template -->
                @yield('content')


            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
            <span class="text-primary font-weight-bold">
                Tous Droits Réservés - Copyright © 2025; - Distribox Entreprise -
                 Contact : +225 0779667033/ 2721266506 - Localisation : TBoulevard Marcory VGE Je suis SCI SERHAN. Abidjan, Côte d'Ivoire
                -
                Développé par <a href="https://christianportfolio.ciatci.com" class="text-primary font-weight-bold" target="_blank">Aka Christian</a>
            </span>
                </div>
            </div>
        </footer>

        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


<!-- Bootstrap core JavaScript-->
<script src="{{asset('front/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('front/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('front/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('front/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
<!-- Core plugin JavaScript-->
<script src="{{asset('front/vendor/jquery-easing/jquery.easing.min.js')}}"></script>
<!-- Custom scripts for all pages-->
<script src="{{asset('front/js/sb-admin-2.min.js')}}"></script>


@yield('js')

</body>

</html>


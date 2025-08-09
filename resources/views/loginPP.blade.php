<!doctype html>

<html lang="fr">
<head>



    <!-- Custom fonts for this template-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('login/fonts/icomoon/style.css')}}">

    <link rel="stylesheet" href="{{asset('login/css/owl.carousel.min.css')}}">

    <link rel="icon" href="{{asset('front/logoEE.jpg')}}">
    <title>PLATE-FORME E-IMMO</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('login/css/bootstrap.min.css')}}">

    <!-- Style -->
    <link rel="stylesheet" href="{{asset('login/css/style.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
<div class="d-lg-flex half">
    <div class="bg order-1 order-md-4" style="background-image: url('{{asset('login/images/gestion.jpg')}}');"></div>
    <div class="contents order-2 order-md-2">

        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-8">
                    <h3 class="text-primary ">CONNEXION A <strong>E-IMMO</strong></h3>
                    <p class="mb-4 font-weight-bold text-dark">BIENVENUE SUR LA PLATEFORME DE GESTION IMMOBILIÈRE</p>


                    @if(Session::has('success'))
                        <br>
                        <div class="alert wrap-input100 validate-input m-b-26 alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ Session::get('success') }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{route('connecter')}}" method="post">
                        @csrf
                        <div class="form-group first">
                            <label for="username">Utilisateur</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group last mb-3">
                            <label for="password">Mot de passe</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <div class="d-flex mb-5 align-items-center">

                        </div>

                        <input type="submit" value="CONNEXION" class="btn btn-block btn-primary">

                    </form>
                </div>
            </div>
        </div>
    </div>


</div>



<script src="{{asset('login/js/jquery-3.3.1.min.js')}}"></script>
<script src="{{asset('login/js/popper.min.js')}}"></script>
<script src="{{asset('login/js/bootstrap.min.js')}}"></script>
<script src="{{asset('login/js/main.js')}}"></script>
</body>
</html>

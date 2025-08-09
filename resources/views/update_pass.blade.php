@extends('layout')
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-primary font-weight-bold text-uppercase active" aria-current="page">Mot de passe</li>
        </ol>
    </nav>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="card shadow mb-4">
        <div class="card-header bg-primary py-3">
            <span class="m-0 font-weight-bold text-white ">Nouveau mot de passe</span>
        </div>
        <div class="card-body">
            <form action="{{route('admin_modifier')}}" class="forms-sample" method="POST">
                @csrf
                <input type="hidden" name="id_users" value="{{ucfirst(Auth()->user()->id)}}">
                <div class="form-group col-md-4">
                    <label>Mot de passe</label>
                    <input type="password" class="form-control" required autocomplete="off" name="password">
                </div>
                <div align="right">
                    <a href="{{route('dashboard')}}" class="btn btn-info" type="reset">Retour</a>
                    <button  class="btn btn-primary" type="submit">Modifier</button>
                </div>

            </form>

        </div>
    </div>

@endsection
